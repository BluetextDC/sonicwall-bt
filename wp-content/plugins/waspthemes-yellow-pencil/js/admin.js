!function(t){"use strict";t(document).ready(function(){0==t("body").hasClass("post-type-attachment")&&0<t("#postbox-container-1").length&&(t(document).on("click",".yp-btn",function(){var e=null;0<t("#sample-permalink a").length&&(e=t("#sample-permalink a").attr("href")),null!=e&&null!=e&&""!=e||(e=t("#post-preview").attr("href")),t(window).off("beforeunload.edit-post"),wp.autosave.server.tempBlockSave(),t("form#post").submit(),-1!=e.indexOf("://")&&(e=e.split("://")[1]);var o=t("#post_ID").val();e="admin.php?page=yellow-pencil-editor&href="+encodeURIComponent(e)+"&yp_page_id="+o+"&yp_page_type="+typenow+"&yp_mode=single",window.open(e,"_blank")}),t("#postbox-container-1").prepend("<a class='yp-btn'><span class='dashicons dashicons-admin-appearance'></span>Edit Page - YellowPencil</a>")),t("body").hasClass("block-editor-page")&&(window.ypLoaderBlock=setInterval(function(){0<t(".edit-post-header-toolbar").length&&(clearInterval(window.ypLoaderBlock),t(document).on("click",".yp-btn",function(){0<t(".editor-post-save-draft").length&&t(".editor-post-save-draft").trigger("click");var e="admin.php?page=yellow-pencil-editor&href&yp_page_id="+t("#post_ID").val()+"&yp_page_type="+typenow+"&yp_mode=single";window.open(e,"_blank")}),0<t(".edit-post-header-toolbar__block-toolbar").length?t(".edit-post-header-toolbar__block-toolbar").before("<button type='button' class='components-button components-icon-button yp-btn'><span class='dashicons dashicons-admin-appearance'></span></button>"):t(".edit-post-header-toolbar").append("<button type='button' class='components-button components-icon-button yp-btn'><span class='dashicons dashicons-admin-appearance'></span></button>"))},300))})}(jQuery);