!function(e,o){"use strict";function t(o){this.$region=e(o),this.region=this.$region.data("region"),this.$body=e("body"),this.setup()}e.extend(t,{instances:[]}),e.extend(t.prototype,{full:!1,setup:function(){var e=this;if(e.$region.find(".escort-item").length){var o,t;e.$region.filter(".escort-vertical").on("mouseenter.escort",function(r){r.preventDefault(),t=e.$region.hasClass("escort-instant")?0:400,o=setTimeout(function(){e.showFull()},t)}).on("mouseleave",function(e){e.preventDefault(),clearTimeout(o)})}else e.$region.remove(),e.$body.removeClass("has-escort-"+this.region)},showFull:function(){var o=this;o.full||(o.full=!0,o.$body.addClass("show-escort-full-"+o.region),o.$body.on("click.escort-"+o.region,function(t){o.full&&!e(t.target).closest(o.$region).length&&o.hideFull()}),o.$body.trigger("escort-region:show"))},hideFull:function(){var e=this;e.full&&(e.full=!1,e.$body.removeClass("show-escort-full-"+e.region),e.$body.off("click.escort-"+e.region),e.$body.trigger("escort-region:hide",[e.$region]))}}),Drupal.behaviors.escort={attach:function(o){var r=e(o).find(".escort-region").once("escort-region").addClass("escort-region-processed");if(r.length){for(var n=0;n<r.length;n++)t.instances.push(new t(r[n]));setTimeout(function(){e("body").addClass("escort-ready")},10)}}},Drupal.Escort=t}(jQuery,document);