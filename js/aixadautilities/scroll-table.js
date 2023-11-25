function ScrollTable(table, settings = {}) {
  this.table = table;
  this.shadow = table.cloneNode(true);
  this.settings = settings;
  return this;
}

ScrollTable.prototype.show = function () {
  const [height] = this.parseHeight(this.settings.height);
  if (!this.table.offsetHeight || this.table.offsetHeight <= height) return;

  $(this.table).on("hide", this.destroy.bind(this));

  const viewboxStyle = { ...ScrollTable.viewboxStyle };
  viewboxStyle.maxHeight = this.formatHeight(this.settings.height);
  const overlayStyle = { ...ScrollTable.overlayStyle };
  overlayStyle.maxHeight = viewboxStyle.maxHeight;

  const wrapper = document.createElement("div");
  ScrollTable.applyStyles(wrapper, ScrollTable.wrapperStyle);
  wrapper.classList.add("scroll-table-wrapper");
  this.table.parentElement.insertBefore(wrapper, this.table);
  this.wrapper = wrapper;

  const viewbox = document.createElement("div");
  ScrollTable.applyStyles(viewbox, viewboxStyle);
  viewbox.classList.add("scroll-table-viewbox");
  wrapper.appendChild(viewbox);
  viewbox.appendChild(this.table);

  const overlay = document.createElement("div");
  ScrollTable.applyStyles(overlay, overlayStyle);
  overlay.classList.add("scroll-table-overlay");
  wrapper.appendChild(overlay);
  overlay.appendChild(this.shadow);

  const shadowHead = Array.from(this.shadow.getElementsByTagName("thead")).pop();
  if (shadowHead === void 0) return;

  for (let i = 0; i < this.shadow.children.length; i++) {
    const child = this.shadow.children[i];
    if (child === shadowHead) continue;
    ScrollTable.applyStyles(child, { visibility: "hidden" });
  }

  this.shadow.style.paddingRight = ScrollTable.scrollbarWidth + "px";

  viewbox.addEventListener("scroll", this.onScroll.bind(this, shadowHead));

  $(this.table).trigger("show");
};

ScrollTable.wrapperStyle = {
  position: "relative",
};

ScrollTable.viewboxStyle = {
  maxHeight: "auto",
  overflow: "visible scroll",
};

ScrollTable.overlayStyle = {
  position: "absolute",
  top: "0px",
  left: "0px",
  pointerEvents: "none",
  width: "100%",
  overflow: "hidden",
};

ScrollTable.shadowHeadStyle = {
  boxShadow: "0px 3px 3px -3px",
};

ScrollTable.applyStyles = function (el, style) {
  Object.keys(style).forEach((key) => {
    el.style[key] = style[key];
  });
};

Object.defineProperty(ScrollTable, "scrollbarWidth", {
  get() {
    if (
      navigator.userAgent.match(/Android/i) ||
      navigator.userAgent.match(/webOS/i) ||
      navigator.userAgent.match(/iPhone/i) ||
      navigator.userAgent.match(/iPad/i) ||
      navigator.userAgent.match(/iPod/i) ||
      navigator.userAgent.match(/BlackBerry/i) ||
      navigator.userAgent.match(/Windows Phone/i)
    ) {
      return 0;
    } else if (/Chrome/.test(navigator.userAgent)) {
      return 15;
    } else if (/Firefox/.test(navigator.userAgent)) {
      return 0;
    } else {
      return 15;
    }
  },
});

ScrollTable.prototype.parseHeight = function (height) {
  if (height === void 0) return [null, null];
  const value = parseFloat(height);
  const unit = String(height).replace(String(value), "") || "px";
  return [value, unit];
};

ScrollTable.prototype.formatHeight = function (height) {
  const [value, unit] = this.parseHeight(height);
  if (!value) return "auto";
  return value + unit;
};

ScrollTable.prototype.onScroll = function (shadowHead, { srcElement }) {
  if (srcElement.scrollTop > 0)
    ScrollTable.applyStyles(shadowHead, ScrollTable.shadowHeadStyle);
  else ScrollTable.applyStyles(shadowHead, { boxShadow: null });
};

ScrollTable.prototype.destroy = function () {
  this.wrapper.parentElement.insertBefore(this.table, this.wrapper);
  this.wrapper.parentElement.removeChild(this.wrapper);
  $(this.table).off("hide");
};

(function () {
  const _hide = $.fn.hide;
  $.fn.hide = function () {
    this.trigger("hide");
    return _hide.apply(this, arguments);
  };
})();
