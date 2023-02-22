<script type="text/javascript">
    <?php if (is_created_session() && get_current_role() == 'Consumer' && configuration_vars::get_instance()->allow_negative_balances == false) : ?>
        (function() {
            var endpoint = "php/ctrl/Account.php";
            var data = {
                oper: "getUfCurrentBalance",
                uf_id: <?= $_SESSION['userdata']['uf_id']; ?>,
            }

            var query = Object.entries(data).map(function(entry) {
                return entry.map(encodeURIComponent).join("=");
            }).join("&");

            var stagewrap = document.getElementById("stagewrap");
            var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState === 4) {
                    if (this.status === 200) {
                        try {
                            onLoad(this.responseXML);
                        } catch (err) {
                            location = location.protocol + "//" + location.hostname;
                        }
                    }
                    stagewrap.classList.remove("hidden");
                }
            }

            ajax.open("POST", endpoint + "?" + query, true);

            ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            ajax.setRequestHeader("Accept", "application/xml, text/xml, */*; q=0.01");
            ajax.withCredentials = true;

            ajax.send();

            function onLoad(doc) {
                var graceDays = <?= configuration_vars::get_instance()->negative_balance_grace_periode; ?>;
                var balance = doc.getElementsByTagName("balance")[0];
                
                if (balance == void 0) return;
                
                var result = parseFloat(balance.textContent);
                if (result < 0) {
                    var lastDate = parseDateTime(doc.getElementsByTagName("last_update")[0].textContent);
                    var lastDateDaysDelta = Math.floor((Date.now() - lastDate.getTime()) / (1e+3 * 60 * 60 * 24));
                    var gracePeriodeDays = <?= configuration_vars::get_instance()->negative_balance_grace_periode; ?>;
                    
                    var disabledPages = <?= json_encode(configuration_vars::get_instance()->negative_balance_disabled_pages); ?>;
                    var isPageDisabled = disabledPages.reduce(function (isDisabled, page) {
                      return isDisabled || "<?= $_SERVER['REQUEST_URI'] ?>".match(new RegExp(page));
                    }, false);
                    
                    if (lastDateDaysDelta > gracePeriodeDays && isPageDisabled) {
                        var newContent = document.createElement("div");
                        newContent.id = "noMoneyBan";
                        newContent.innerHTML = `<img src="img/angry-carrot.jpeg" alt="<?= $Text['negative_balance_image_alt']; ?>">
                        <h1><?= $Text['negative_balance_ban_title']; ?></h1>
                        <h2><?= $Text['negative_balance_ban_subtitle']; ?></h2>`;
                        
                        stagewrap.parentElement.insertBefore(newContent, stagewrap);
                        stagewrap.parentElement.removeChild(stagewrap);
                    } else {
                      var warningNode = document.createElement("h2");
                      warningNode.id = "noMoneyDisclaimer";
                      warningNode.innerHTML =
                          "<?= $Text["negative_balance_disclaimer"] ?>" + new Intl.NumberFormat("<?= configuration_vars::get_instance()->type_formats['numbers']['locale']; ?>", {
                              style: "currency",
                              currency: "<?= configuration_vars::get_instance()->currency_iso; ?>"
                          }).format(result) + "<br><?= $Text["negative_balance_advise"]; ?>";

                      document.getElementById("wrap")
                          .insertBefore(warningNode, document.getElementById("stagewrap"));
                    }
                }
            }

            function parseDateTime(dt) {
                return new Date(dt);
            }
        })();
    <?php else: ?>
        document.getElementById("stagewrap").classList.remove("hidden");
    <?php endif; ?>
</script>
