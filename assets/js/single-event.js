jQuery(document).ready(function () {
    date = jQuery('.event-countdown').attr('date');
    var countDownDate = new Date(date).getTime();

    // Update the count down every 1 second
    var x = setInterval(function () {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

        // Display the result in the element with id="demo"
        jQuery('.countdown-days').html(days);
        jQuery('.countdown-hours').html(hours);
        jQuery('.countdown-minutes').html(minutes);

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            jQuery('.event-countdown-holder').html('EXPIRED');
        }
    }, 1000);
});