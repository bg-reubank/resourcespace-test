<?php
$lang['emu_configuration'] = 'EMu Configuration';
$lang['emu_mapping_rules'] = 'Mapping rules';

$lang['emu_api_settings'] = 'API server settings';
$lang['emu_api_server'] = 'Server address (e.g. http://[server.address])';
$lang['emu_api_server_port'] = 'Server port';

$lang['emu_resource_types'] = 'Select resource types linked to EMu';
$lang['emu_email_notify'] = 'E-mail address that script will send notifications to. Leave blank to default to the system notification address';
$lang['emu_script_failure_notify_days'] = 'Number of days after which to display alert and send e-mail if script has not completed';

$lang['emu_script_header'] = 'Enable script that will automatically update the EMu data whenever ResourceSpace runs its scheduled task (cron_copy_hitcount.php)';
$lang['emu_last_run_date'] = '<div class="Question"><label><strong>Script last run</strong></label><input name="script_last_ran" type="text" value="%script_last_ran%" disabled style="width: 300px;"></div><div class="clearerleft"></div>';
$lang['emu_enable_script'] = 'Enable EMu script';
$lang['emu_test_mode'] = 'Test mode - Set to true and script will run but not update resources';
$lang['emu_interval_run'] = 'Run script at the following interval (e.g. 1 day, 2 weeks, fortnight). Leave blank and it will run everytime cron_copy_hitcount.php runs)';

$lang['emu_settings_header'] = 'EMu settings';
$lang['emu_irn_field'] = 'Metadata field used to store the EMu identifier (IRN)';

// Errors
$lang['emu_script_problem'] = 'WARNING - the EMu script has not successfully completed within the last %days% days. Last run time: ';
$lang['emu_no_resource'] = 'No resource ID specified!';