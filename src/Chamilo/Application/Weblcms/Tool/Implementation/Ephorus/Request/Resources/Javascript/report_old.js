/**
 * Handles the printing of the report
 * @author Pieterjan Broekaert Hogent
 */

$(
    document
).ready(
    function (
        )
    {
        var print_button = $(
            ".print_button"
        );
        print_button.on(
            "click", printReport
        );

        function printReport(
            )
        {
            var printContents = document.getElementById(
                "printable"
            ).innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print(
            );

            document.body.innerHTML = originalContents;
        }
    }
)