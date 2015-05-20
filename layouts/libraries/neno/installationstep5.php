<?php

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');

?>

<style>
	#task-messages {
		height: 500px;
		overflow: auto;
        background-color: #f5f5f5;
        padding: 20px;
        color: #808080;
	}


    
	.log-level-2 {
		margin-left: 20px;
        font-weight: bold;
        margin-top: 16px;
	}

	.log-level-3 {
		margin-left: 40px;
	}
</style>

<div class="installation-step">
	<div class="installation-body span12">
		<div class="error-messages"></div>
		<h2><?php echo JText::_('COM_NENO_INSTALLATION_SETUP_COMPLETING_TITLE'); ?></h2>

		<div class="progress progress-striped active" id="progress-bar">
			<div class="bar" style="width: 0%;"></div>
		</div>
		<p><?php echo JText::_('COM_NENO_INSTALLATION_SETUP_COMPLETING_FINISH_SETUP_MESSAGE'); ?></p>

		<div id="task-messages">

		</div>
	</div>

	<?php echo JLayoutHelper::render('installationbottom', 4, JPATH_NENO_LAYOUTS); ?>
</div>

<script>
	jQuery.ajax({
		url: 'index.php?option=com_neno&task=installation.finishingSetup',
		success: function (data) {
			checkStatus();
			processInstallationStep();
			window.clearInterval(interval);
		}
	});

	interval = window.setInterval(checkStatus, 2000);

	function checkStatus() {
		jQuery.ajax({
			url: 'index.php?option=com_neno&task=installation.getSetupStatus',
			dataType: 'json',
			success: function (data) {
				for (var i = 0; i < data.length; i++) {
                    var log_line = jQuery('#installation-status-' + data[i].level).clone().removeAttr('id').html(data[i].message);
                    if (data[i].level == 1) {
                        log_line.addClass('alert-' + data[i].type);
                    }
					jQuery('#task-messages').append(log_line);
                    
                    //Scroll to bottom
                    jQuery("#task-messages").stop().animate({
                        scrollTop:jQuery("#task-messages")[0].scrollHeight - jQuery("#task-messages").height()
                    },400);               
                    
					//jQuery('#task-messages').append('<div class="alert alert-level-' + data[i].level + ' alert-' + data[i].type + '">' + data[i].message + '</div>');
				}

				if (data[data.length - 1].percent != 0) {
					jQuery('#progress-bar .bar').width(data[data.length - 1].percent + '%');
				}
			}
		});
	}


</script>

<div class="hidden">
    <!-- Different HTML to show depending on log level -->
    <div id="installation-status-1" class="alert"></div>
    <div id="installation-status-2" class="log-level-2"></div>
    <div id="installation-status-3" class="log-level-3"></div>
</div>