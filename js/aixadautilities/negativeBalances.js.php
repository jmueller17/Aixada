<?php $config = configuration_vars::get_instance(); ?>
<script type="text/javascript">
    <?php if (is_created_session() && get_current_role() == 'Consumer' && isset($config->allow_negative_balances) && $config->allow_negative_balances == false) : ?>
            (function() {
                var endpoint = "php/ctrl/Account.php";
                var data = {
                    oper: "getUfNegativeBalance",
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
                    var graceDays = <?= isset($config->negative_balance_grace_periode) ? (int) $config->negative_balance_grace_periode : 14 ?>;
                    var balance = doc.getElementsByTagName("balance")[0];

                    if (balance == void 0) return;

                    var result = parseFloat(balance.textContent);
                    if (result < 0) {
                        var lastDate, lastDateDaysDelta;
                        try {
                            lastDate = parseDateTime(doc.getElementsByTagName("date")[0].textContent);
                            lastDateDaysDelta = Math.floor((Date.now() - lastDate.getTime()) / (1e+3 * 60 * 60 * 24));
                        } catch (err) {
                            console.error(err);
                        }


                        var disabledPages = <?= isset($config->negative_balance_disabled_pages) ? json_encode($config->negative_balance_disabled_pages) : '[]'; ?>;
                        var isPageDisabled = disabledPages.reduce(function(isDisabled, page) {
                            <?php $page_uri = $_SERVER['REQUEST_URI'] == '/' ? 'index.php' : $_SERVER['REQUEST_URI']; ?>
                            return isDisabled || "<?= $page_uri; ?>".match(new RegExp(page));
                        }, false);

                        if (validateDate(lastDate) && lastDateDaysDelta > graceDays && isPageDisabled) {
                            var newContent = document.createElement("div");
                            newContent.id = "noMoneyBan";
                            newContent.innerHTML = `<img src="img/angry-carrot.jpeg" alt="<?= $Text['negative_balance_image_alt']; ?>">
                        <h1><?= $Text['negative_balance_ban_title']; ?></h1>
                        <h2><?= $Text['negative_balance_ban_subtitle']; ?></h2>`;

                            stagewrap.parentElement.insertBefore(newContent, stagewrap);
                            stagewrap.parentElement.removeChild(stagewrap);
                        } else {
                            var locale = "<?= isset($config->type_formats['numbers']['locale']) ? $config->type_formats['numbers']['locale'] : ''; ?>" || navigator.language;
                            var currency = "<?= isset($config->currency_iso) ? $config->currency_iso : 'EUR'; ?>";
                            var warningNode = document.createElement("h2");
                            warningNode.id = "noMoneyDisclaimer";
                            warningNode.innerHTML =
                                "<?= $Text["negative_balance_disclaimer"] ?>" + new Intl.NumberFormat(locale, {
                                    style: "currency",
                                    currency: currency,
                                }).format(result) + "<br><?= $Text["negative_balance_advise"]; ?>";

                            document.getElementById("wrap")
                                .insertBefore(warningNode, document.getElementById("stagewrap"));
                        }
                    }
                }

                function validateDate(d) {
                    return d instanceof Date && ! isNaN(d.valueOf());
                }

                function parseDateTime(dt) {
                    var date;

                    date = new Date(dt);
                    if (!validateDate(date)) {
                        date = new Date(parseInt(dt, 10) * 1e3);
                    }

                    if (!validateDate(date)) {
                        throw new Error("Invalid date: " + String(dt));
                    }

                    return date;
                }
            })();
    <?php else : ?>
        document.getElementById("stagewrap").classList.remove("hidden");
    <?php endif; ?>
</script>
