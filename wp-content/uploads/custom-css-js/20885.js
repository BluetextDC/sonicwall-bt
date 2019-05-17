<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function () {

    function webToCase(fields) {
        var form = document.createElement("form");
        form.method = "POST";
        form.action = "https://webto.salesforce.com/servlet/servlet.WebToCase?encoding=UTF-8";
        for (var fieldName in fields) {
            var theInput = document.createElement("input");
            theInput.name = fieldName;
            theInput.value = fields[fieldName];
            theInput.setAttribute("type", "hidden");
            form.appendChild(theInput);
        }
        document.body.appendChild(form);
        form.submit();
    }

    function getFormFields() {
        var formFields = {
            orgid: '00D410000005bTv', //Put your own OrgId here
            external: 1,
            retURL: location.protocol + "//" + location.host + "/contact-support/customer-service/thank-you/",
            origin: "web",
            priority: "P3",
            status: "Open",
            recordType: "012410000012VOs",
            how_can_we_help: jQuery('select.how-can-we-help').val(),
            name: jQuery('input.first-name').val() + " " + jQuery('input.last-name').val(),
            company: jQuery('input.company-name').val(),
            email: jQuery('input.email').val(),
            phone: jQuery('input.phone').val(),
            subject: jQuery('.request-title').val(),
            description: jQuery('.problem-description').val()
        }

        return formFields;
    }

    jQuery('input[type="submit"]').on("click", function (e) {
        e.preventDefault();

            var formFieldSet = getFormFields();
            console.log(formFieldSet);
            webToCase(formFieldSet);

    });

});
</script>
<!-- end Simple Custom CSS and JS -->
