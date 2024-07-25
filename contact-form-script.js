jQuery(document).ready(function($) {
    function updatePhoneInput() {
        $.ajax({
            url: 'https://ipapi.co/json/',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var countryCode = data.country_calling_code;
                var countryFlag = getCountryFlag(data.country_code);
                var countryName = data.country_code; // Using country code

                $('#phone').val(countryCode + ' ');
                $('#flag').text(countryFlag); // Displaying the flag
                $('#country-name').text(countryName); // Displaying the country code

                console.log('Country data loaded:', data);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching country data:', textStatus, errorThrown);
            }
        });
    }

    function getCountryFlag(countryCode) {
        var flagOffset = 127397;
        return String.fromCodePoint(...[...countryCode.toUpperCase()].map(c => c.charCodeAt() + flagOffset));
    }

    $('#contact-form').on('submit', function(e) {
        e.preventDefault();

        var formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            message: $('#message').val()
        };

        $.post(contactFormAjax.ajaxurl, {
            action: 'submit_contact_form',
            name: formData.name,
            email: formData.email,
            phone: formData.phone,
            message: formData.message
        }, function(response) {
            if (response.success) {
                alert('Form submitted successfully!');
            } else {
                alert('Error submitting form.');
            }
        });
    });

    // Call updatePhoneInput when the document is ready
    updatePhoneInput();
});
