(function($) {
    "use strict";
    $( document ).ready( function () { 
        $("body").on("change",".calculatedformat_enable",function(e){
			if ($(this).is(':checked')) { 
				$(".calculatedformat").removeClass("hidden");
			}else{
				$(".calculatedformat").addClass("hidden");
			}
		})
		$(".cf7_import_demo_calculator").click(function(event) {
			/* Act on the event */
			event.preventDefault();
			if (confirm('It will overwrite the current content! Do you want to do it?')) {
			    $("#wpcf7-form").val(cf7_calculator.data);
			    $("#contact-form-editor-tabs li").first().find('a').click();
			} 
		});

    })

    setTimeout(() => {
      if (typeof contact_form_7_calculator_name !== 'undefined' && contact_form_7_calculator_name !== null) {
         var tributeAttributes = {
            autocompleteMode: true,
            noMatchTemplate: "",
            values: contact_form_7_calculator_name,
            selectTemplate: function(item) {
              if (typeof item === "undefined") return null;
              if (this.range.isContentEditable(this.current.element)) {
                return (
                  '<span contenteditable="false"><a>' +
                  item.original.key +
                  "</a></span>"
                );
              }

              return item.original.value;
            },
            menuItemTemplate: function(item) {
              return item.string;
            }
          };
          var tributeAutocompleteTestArea = new Tribute(
            Object.assign(
              {
                menuContainer: document.getElementById("autocomplete-textarea-container"),
                replaceTextSuffix: "",
              },
              tributeAttributes
            )
          );
          tributeAutocompleteTestArea.attach(
            document.getElementById("autocomplete-textarea")
          );
        }
      },1000);
    

})(jQuery);
