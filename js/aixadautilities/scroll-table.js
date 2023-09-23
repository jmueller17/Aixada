function ScrollTable(table, settings) {
  this.table = table;
  this.settings = settings;
  return this;
}

ScrollTable.prototype.show = function () {
  $(this.table).on("hide", this.destroy.bind(this));

  const viewboxStyle = { ...ScrollTable.viewboxStyle };
  viewboxStyle.maxHeight = this.formatHeight(this.settings.height);
  const overlayStyle = { ...ScrollTable.overlayStyle };
  overlayStyle.maxHeight = viewboxStyle.maxHeight;

  const wrapper = document.createElement("div");
  this.applyStyles(wrapper, ScrollTable.wrapperStyle);
  wrapper.classList.add("scroll-table-wrapper");
  this.table.parentElement.insertBefore(wrapper, this.table);
  this.wrapper = wrapper;

  const viewbox = document.createElement("div");
  this.applyStyles(viewbox, viewboxStyle);
  viewbox.classList.add("scroll-table-viewbox");
  wrapper.appendChild(viewbox);
  viewbox.appendChild(this.table);

  const overlay = document.createElement("div");
  this.applyStyles(overlay, overlayStyle);
  overlay.classList.add("scroll-table-overlay");
  wrapper.appendChild(overlay);
  overlay.appendChild(this.table.cloneNode(true));

  const shadowTable = overlay.children[0];
  const head = shadowTable.getElementsByTagName("thead");
  if (!head.length) return;
  this.applyStyles(head[0], ScrollTable.shadowHeadStyle);

  for (let i = 0; i < shadowTable.children.length; i++) {
    const child = shadowTable.children[i];
    if (child === head[0]) continue;
    this.applyStyles(child, { visibility: "hidden" });
  }

  this.setScrollbarSpan(shadowTable);
  $(this.table).trigger("show");
};

ScrollTable.wrapperStyle = {
  position: "relative",
};

ScrollTable.viewboxStyle = {
  maxHeight: "auto",
  overflowY: "scroll",
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

ScrollTable.prototype.formatHeight = function (height) {
  if (height === void 0) return "auto";

  const value = parseFloat(height);
  const unit = String(height).replace(String(value), "");
  if (unit) return height;
  return height + "px";
};

ScrollTable.prototype.applyStyles = function (el, style) {
  Object.keys(style).forEach((key) => {
    el.style[key] = style[key];
  });
};

ScrollTable.prototype.setScrollbarSpan = function (table) {
  if (/Chrome/.test(navigator.userAgent)) {
    fix = 15;
  } else if (/Firefox/.test(navigator.userAgent)) {
    fix = 0;
  } else {
    fix = 15;
  }

  table.style.paddingRight = fix + "px";
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
