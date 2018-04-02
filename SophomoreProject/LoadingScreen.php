<?php
/**
 * Created By: William Watson
 * Description: Loading screen while watson is training.
 */
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require('classes/IBMWatsonFunctionality.php');
require('templates/header.html');
?>
<h1>Watson is currently training...</h1>

<script>
    $( document ).ready(function() {
        var watsonIsTraining = "<?php echo json_encode(IBMWatsonFunctionality::isWatsonTraining()) ?>";

        while(watsonIsTraining == 'true'){
            watsonIsTraining = "<?php echo json_encode(IBMWatsonFunctionality::isWatsonTraining()) ?>";
            console.log('Hello');
            if(watsonIsTraining == 'false'){
                window.location = 'index.php';
            }
        }
    });

</script>

<?php
require('templates/footer.html');
?>
