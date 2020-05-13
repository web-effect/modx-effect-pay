<?php
/**
 * Build the setup options form.
 *
 * @subpackage build
 */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        /*$setting = $modx->getObject('modSystemSetting',array('key' => 'quip.emailsTo'));
        if ($setting != null) { $values['emailsTo'] = $setting->get('value'); }
        unset($setting);*/
    break;
    case xPDOTransport::ACTION_UNINSTALL: break;
}

$output = '
<div class="panel-desc">
    <p>Это опции компонента Simple</p>
</div>
<label for="sample-option">Опция:</label>
<input type="text" name="sampleOption" id="sample-option" width="300" value="" />
';
return $output;