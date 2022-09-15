<?php
namespace Bitrix\Landing\Components\LandingEdit;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	die();
}

/** @var array $arResult */
/** @var array $arParams */
/** @var \CMain $APPLICATION */
/** @var \LandingSiteEditComponent $component */

use Bitrix\Landing\Domain;
use Bitrix\Landing\Domain\Register;
use Bitrix\Landing\File;
use Bitrix\Landing\Hook\Page\Cookies;
use Bitrix\Landing\Manager;
use Bitrix\Landing\Restriction;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\Web\Uri;
use CBXPunycode;
use CJSCore;
use CUtil;
use function htmlspecialcharsback;
use function htmlspecialcharsbx;
use function randString;

Loc::loadMessages(__FILE__);

if ($arResult['ERRORS'])
{
	?><div class="landing-message-label error"><?
	foreach ($arResult['ERRORS'] as $error)
	{
		echo $error . '<br/>';
	}
	?></div><?php
}
if ($arResult['FATAL'])
{
	return;
}

// vars
$row = $arResult['SITE'];
$hooks = $arResult['HOOKS'];
$tplRefs = $arResult['TEMPLATES_REF'];
$isIntranet = $arResult['IS_INTRANET'];
$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$isSMN = $row['TYPE']['CURRENT'] == 'SMN';
$domain = isset($arResult['DOMAINS'][$row['DOMAIN_ID']['CURRENT']])
		? $arResult['DOMAINS'][$row['DOMAIN_ID']['CURRENT']]
		: [];
$availableOnlyForZoneRu = Manager::availableOnlyForZone('ru');
$isAjax = $component->isAjax();

// title
if ($arParams['SITE_ID'])
{
	Manager::setPageTitle($component->getMessageType('LANDING_TPL_TITLE_EDIT'));
}
else
{
	Manager::setPageTitle($component->getMessageType('LANDING_TPL_TITLE_ADD'));
}

Extension::load([
	'ui.design-tokens',
	'ui.fonts.opensans',
	'color_picker',
	'landing_master',
	'action_dialog',
	'access',
	'sidepanel',
	'landing.metrika',
	'ui.buttons',
	'ui.dialogs.messagebox',
	'ui.layout-form',
	'ui.dialogs.messagebox',
	'ui.forms',
	'ui.hint',
]);

Asset::getInstance()->addCSS('/bitrix/components/bitrix/landing.site_edit/templates/.default/landing-forms.css');
Asset::getInstance()->addJS('/bitrix/components/bitrix/landing.site_edit/templates/.default/landing-forms.js');

$bodyClass = $APPLICATION->GetPageProperty('BodyClass');
$APPLICATION->SetPageProperty(
	'BodyClass',
	($bodyClass ? $bodyClass.' ' : '') . 'landing-slider-frame-popup'
);

$this->getComponent()->initAPIKeys();

// view-functions
include 'template_class.php';
$template = new Template($arResult);

// some url
$uriSave = new Uri(htmlspecialcharsback(POST_FORM_ACTION_URI));
$uriSave->addParams(array(
	'action' => 'save'
));
$uriDomain = new Uri(
	str_replace('#site_edit#', $row['ID']['CURRENT'], $arParams['PAGE_URL_SITE_DOMAIN'])
);
$uriDomain->addParams(array(
	'tab' => '__tab__',
	'IFRAME' => 'Y'
));
$uriCookies = new Uri(
	str_replace('#site_edit#', $row['ID']['CURRENT'], $arParams['PAGE_URL_SITE_COOKIES'])
);
$uriCookies->addParams([
	'IFRAME' => 'Y'
]);

?>
<script type="text/javascript">
	BX.ready(function(){
		const editComponent = new BX.Landing.EditComponent('<?= $template->getFieldId('ACTION_CLOSE') ?>');
		top.window['landingSettingsSaved'] = false;
		<?if ($arParams['SUCCESS_SAVE']):?>
		top.window['landingSettingsSaved'] = true;
		top.BX.onCustomEvent('BX.Landing.Filter:apply');
		editComponent.actionClose();
		if (typeof top.BX.Landing.UI !== 'undefined' && typeof top.BX.Landing.UI.Tool !== 'undefined')
		{
			top.BX.Landing.UI.Tool.ActionDialog.getInstance().close();
		}
		<?endif;?>
		BX.Landing.Env.createInstance({
			params: {type: '<?= $arParams['TYPE'] ?>'}
		});
	});
</script>

<?php
if ($arParams['SUCCESS_SAVE'])
{
	if ($request->get('IFRAME') != 'Y')
	{
		$this->getComponent()->refresh([], ['action']);
	}
	return;
}
?>
<div class="landing-form-wrapper">
	<form
		method="post"
		action="/bitrix/tools/landing/ajax.php?action=Site::uploadFile"
		enctype="multipart/form-data"
		id="landing-form-favicon-form">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="data[id]" value="<?=$arParams['SITE_ID']?>"/>
		<input type="file" name="picture" id="landing-form-favicon-input" style="display: none;"
	>
	</form>
	<form
		action="<?=htmlspecialcharsbx($uriSave->getUri())?>"
		method="post"
		class="landing-form landing-form-gray-padding landing-form-collapsed"
		id="landing-site-set-form"
	>
		<?= bitrix_sessid_post() ?>
		<input type="hidden" name="fields[SAVE_FORM]" value="Y" />
		<input type="hidden" name="fields[TYPE]" value="<?= $row['TYPE']['CURRENT'] ?>" />
		<input type="hidden" name="fields[CODE]" value="<?= $row['CODE']['CURRENT'] ?>" />

		<!--Title-->
		<div class="landing-form-title-block">
			<span class="landing-editable-field" id="<?= $template->getFieldId('EDITABLE_TITLE') ?>">
				<label class="landing-editable-field-label landing-editable-field-label-js">
					<?=$row['TITLE']['CURRENT']?>
				</label>
				<input type="text"
					name="fields[TITLE]"
					class="ui-input landing-editable-field-input landing-editable-field-input-js"
					value="<?=$row['TITLE']['CURRENT']?>"
					placeholder="<?=$row['TITLE']['TITLE']?>"/>
				<span class="ui-title-input-btn ui-title-input-btn-js ui-editing-pen"></span>
			</span>
		</div>

		<div class="ui-form ui-form-section">
			<!--Domain-->
			<?php if ($isIntranet):?>
				<div class="ui-form-row">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $component->getMessageType('LANDING_TPL_TITLE_ADDRESS_SITE') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<span class="landing-form-site-name-label">
							<?= Domain::getHostUrl() ?><?= Manager::getPublicationPath() ?>
						</span>
						<input type="text" name="fields[CODE]" class="ui-input" value="<?= trim($row['CODE']['CURRENT'], '/') ?>" placeholder="<?= $row['TITLE']['TITLE'] ?>" />
						<span class="landing-form-site-name-label">/</span>
					</div>
				</div>
			<?php elseif ($domain):?>
			<div class="ui-form-row ui-form-row-middle-input">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $row['CODE']['TITLE']?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="landing-domain">
						<?php if (Manager::isB24()): ?>
							<?php $puny = new CBXPunycode; ?>
							<span class="landing-domain-name">
								<span class="landing-domain-name-value"><?= $puny->decode($domain['DOMAIN']) ?></span>
								<a href="<?= str_replace('__tab__', '', $uriDomain->getUri()) ?>" class="ui-title-input-btn ui-editing-pen landing-frame-btn"></a>
							</span>
								<?php if (!Domain::getBitrix24Subdomain($domain['DOMAIN'])):?>
								<?php if (Register::isDomainActive($domain['DOMAIN'])):?>
									<div class="landing-domain-status landing-domain-status-active">
										<div class="landing-domain-status-text"><?= Loc::getMessage('LANDING_TPL_DOMAIN_ACTIVATION_YES') ?></div>
									</div>
								<?php else:?>
									<div class="landing-domain-status landing-domain-status-wait">
										<div class="landing-domain-status-text"><?= Loc::getMessage('LANDING_TPL_DOMAIN_ACTIVATION_NO') ?></div>
										<div class="landing-domain-status-notice"><?= Loc::getMessage('LANDING_TPL_DOMAIN_ACTIVATION_INFO') ?></div>
									</div>
								<?php endif;?>
								<?php elseif ($arResult['REGISTER']->enable()):?>
									<div class="landing-domain-status landing-domain-status-configure">
										<div class="landing-domain-status-title"><?= Loc::getMessage('LANDING_TPL_DOMAIN_FREE_TEXT') ?></div>
										<a href="<?= str_replace('__tab__', 'provider', $uriDomain->getUri()) ?>" class="ui-btn ui-btn-primary ui-btn-sm ui-btn-round landing-frame-btn">
											<?= Loc::getMessage('LANDING_TPL_DOMAIN_FREE_BUTTON') ?>
										</a>
										<a href="<?= str_replace('__tab__', 'private', $uriDomain->getUri()) ?>" class="ui-btn ui-btn-light-border ui-btn-sm ui-btn-round landing-frame-btn">
											<?= Loc::getMessage('LANDING_TPL_DOMAIN_PRIVATE_BUTTON') ?>
										</a>
									</div>
								<?php else:?>
									<div>
										<a href="<?= str_replace('__tab__', 'private', $uriDomain->getUri()) ?>" class="ui-btn ui-btn-light-border ui-btn-sm ui-btn-round landing-frame-btn">
											<?= Loc::getMessage('LANDING_TPL_DOMAIN_PRIVATE_BUTTON') ?>
										</a>
									</div>
									<script>
										BX.ready(function() {
											var domainBlock = document.querySelector('.landing-domain');
											domainBlock.classList.add('landing-domain-own');
										});
									</script>
								<?php endif;?>

						<?php else:?>
							<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
								<div class="ui-ctl-after ui-ctl-icon-angle "></div>
								<select name="fields[DOMAIN_ID]" class="ui-ctl-element">
										<?foreach ($arResult['DOMAINS'] as $item):?>
										<option
											value="<?= $item['ID']?>"
											<?if ($item['ID'] == $row['DOMAIN_ID']['CURRENT']){?> selected="selected"<?}?>
										>
												<?= htmlspecialcharsbx($item['DOMAIN']) ?>
											</option>
										<?php endforeach;?>
									</select>
							</div>
						<?php endif;?>
					</div>
					</div>
				</div>
			<?php endif;?>

			<!--Widget-->
			<?php if (isset($hooks['B24BUTTON'])): ?>
				<?php $pageFields = $hooks['B24BUTTON']->getPageFields(); ?>
				<div class="ui-form-row ui-form-row-middle-input" data-landing-main-option="b24widget">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $pageFields['B24BUTTON_CODE']->getLabel() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="landing-form-flex-box landing-form-widget">
							<?php
							$template->showField($pageFields['B24BUTTON_CODE']);
							?>
							<?php if (ModuleManager::isModuleInstalled('crm')):?>
								<a href="/crm/button/" class="landing-form-input-right" target="_blank">
									<?= Loc::getMessage('LANDING_TPL_ACTION_SETTINGS') ?>
								</a>
							<?php elseif (ModuleManager::isModuleInstalled('b24connector')):?>
								<a href="/bitrix/admin/b24connector_b24connector.php?lang=<?= LANGUAGE_ID ?>" class="landing-form-input-right" target="_blank">
									<?= Loc::getMessage('LANDING_TPL_ACTION_SETTINGS') ?>
								</a>
							<?php else:?>
								<a href="/bitrix/admin/module_admin.php?lang=<?= LANGUAGE_ID ?>" class="landing-form-input-right" target="_blank">
									<?= Loc::getMessage('LANDING_TPL_ACTION_INSTALL_B24') ?>
								</a>
							<?php endif;?>
						</div>
					</div>
				</div>

				<div class="ui-form-row ui-form-row-middle-input">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $pageFields['B24BUTTON_COLOR']->getLabel() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-row">
							<?php $template->showField($pageFields['B24BUTTON_COLOR'], ['additional' => 'readonly']); ?>
							<script type="text/javascript">
								BX.ready(function() {
									new BX.Landing.B24ButtonColor(
										BX('<?= $template->getFieldId('B24BUTTON_COLOR') ?>'),
										BX('<?= $template->getFieldId('B24BUTTON_COLOR_VALUE') ?>')
									);
								});
							</script>
						</div>

						<?php $template->showField($pageFields['B24BUTTON_COLOR_VALUE'], ['title' => true]); ?>
						<script type="text/javascript">
							BX.ready(function() {
								new BX.Landing.ColorPicker(BX('<?= $template->getFieldId('B24BUTTON_COLOR_VALUE') ?>'));
							});
						</script>
					</div>
				</div>
			<?php endif; ?>

			<!--Up button-->
			<?php if (isset($hooks['UP'])): ?>
				<?php $pageFields = $hooks['UP']->getPageFields(); ?>
				<div class="ui-form-row">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $pageFields['UP_SHOW']->getLabel()?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php $template->showField($pageFields['UP_SHOW'], [
							'title' => Loc::getMessage('LANDING_TPL_ACTION_SHOW')
						]); ?>
					</div>
				</div>
			<?php endif;?>

			<!--Main page-->
			<div class="ui-form-row ui-form-row-middle-input">
				<div class="ui-form-label">
					<div class="ui-ctl-label-text">
						<?= Loc::getMessage('LANDING_TPL_PAGE_INDEX')?>
					</div>
				</div>
				<div class="ui-form-content">
					<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown ui-ctl-w100">
						<div class="ui-ctl-after ui-ctl-icon-angle "></div>
						<select name="fields[LANDING_ID_INDEX]" class="ui-ctl-element ui-field-b24button_color">
							<?php foreach ($arResult['LANDINGS'] as $item):
								if ($item['IS_AREA'])
								{
									continue;
								}
								?>
								<option value="<?= $item['ID']?>"<?php if ($item['ID'] == $row['LANDING_ID_INDEX']['CURRENT']){?> selected="selected"<?php }?>>
									<?= htmlspecialcharsbx($item['TITLE'])?>
								</option>
							<?php endforeach;?>
						</select>
					</div>
				</div>
			</div>
		</div>

		<!--Additional labels-->
		<div class="landing-form-additional-fields landing-form-collapse-block landing-form-additional-fields-js">
				<span class="landing-form-collapse-label"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL') ?></span>
				<span class="landing-additional-alt-promo-wrap">
					<?php if (isset($hooks['FAVICON'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="favicon">Favicon</span>
					<?php endif;?>
					<?php if (isset($hooks['METAGOOGLEVERIFICATION']) || isset($hooks['METAYANDEXVERIFICATION'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="verification"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_VERIFICATION') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['YACOUNTER']) || isset($hooks['GACOUNTER']) || isset($hooks['GTM'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="metrika"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_METRIKA') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['PIXELFB']) || isset($hooks['PIXELVK'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="pixel"><?= Loc::getMessage('LANDING_TPL_HOOK_PIXEL') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['GMAP']) || isset($hooks['YMAP'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="map_required_key"><?= Loc::getMessage('LANDING_TPL_HOOK_GMAP') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['VIEW'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="view"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_VIEW') ?></span>
					<?php endif;?>
					<?php if ($arResult['TEMPLATES']):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="layout"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_LAYOUT') ?></span>
					<?php endif;?>
					<?php if (!$isIntranet && !empty($arResult['LANG_CODES']) && $row['LANG']):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="lang"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_LANG') ?></span>
					<?php endif;?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="404"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_404') ?></span>
					<?php if (isset($hooks['ROBOTS']) && !$isSMN):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="robots"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_ROBOTS') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['SPEED'])):?>
					<span class="landing-additional-alt-promo-text" data-landing-additional-option="speed"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_SPEED') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['HEADBLOCK'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="public_html_disallowed">HTML</span>
					<?php endif;?>
					<?php if (isset($hooks['CSSBLOCK'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="css">CSS</span>
					<?php endif;?>
					<?php if (!$isIntranet):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="off"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_OFF') ?></span>
					<?php endif;?>
					<?php if (isset($hooks['COOKIES'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="cookies">Cookies</span>
					<?php endif;?>
					<?php if (isset($hooks['COPYRIGHT'])):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="sign"><?= Loc::getMessage('LANDING_TPL_ADDITIONAL_SIGN') ?></span>
					<?php endif;?>
					<?php if ($arResult['SHOW_RIGHTS']):?>
						<span class="landing-additional-alt-promo-text" data-landing-additional-option="access"><?= Loc::getMessage('LANDING_TPL_HOOK_RIGHTS_LABEL') ?></span>
					<?php endif;?>
				</span>
			</div>

		<div class="ui-form ui-form-section landing-form-additional">
			<!--Favicon-->
			<?php if (isset($hooks['FAVICON'])): ?>
				<?php $pageFields = $hooks['FAVICON']->getPageFields(); ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="favicon">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['FAVICON']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-row ui-form-row-line">
							<div class="landing-form-favicon-wrap">
								<?php $favId = (int) $pageFields['FAVICON_PICTURE']->getValue();?>
								<img src="<?= $favId > 0 ? File::getFilePath($favId) : '/bitrix/images/1.gif' ?>" alt="" width="32" id="landing-form-favicon-src" />
							</div>
							<input type="hidden" name="fields[ADDITIONAL_FIELDS][FAVICON_PICTURE]" id="landing-form-favicon-value" value="<?= $favId ?>" />
							<a href="#" id="landing-form-favicon-change">
								<?= Loc::getMessage('LANDING_TPL_HOOK_FAVICON_EDIT') ?>
							</a>
							&nbsp;
							<span id="landing-form-favicon-error">(*.png)</span>
						</div>
					</div>
				</div>
			<?php endif;?>

			<!--Yandex, Google verification -->
			<?php if (isset($hooks['METAGOOGLEVERIFICATION'], $hooks['METAYANDEXVERIFICATION'])): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="verification">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_ADDITIONAL_VERIFICATION') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
							$template->showFieldWithToggle('METAGOOGLEVERIFICATION');
							if ($availableOnlyForZoneRu)
							{
								$template->showFieldWithToggle('METAYANDEXVERIFICATION');
							}
						?>
					</div>
				</div>
			<?php endif;?>

			<!--Analytic counters-->
			<?php if (isset($hooks['YACOUNTER'], $hooks['GACOUNTER'], $hooks['GTM'])): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="metrika">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_HOOK_METRIKA') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
						$gaCounterFields = $hooks['GACOUNTER']->getPageFields();
						$fieldId = $template->getFieldId('GACOUNTER');
						if (!$gaCounterFields['GACOUNTER_CLICK_TYPE']->getValue())
						{
							$gaCounterFields['GACOUNTER_CLICK_TYPE']->setValue('text');
						}
						?>
						<div class="ui-form-row" id="<?= $fieldId ?>">
							<?php
							$isLocked = $hooks['GACOUNTER']->isLocked();
							?>
							<div class="ui-form-label landing-form-gacounter-use-js<?= $isLocked ? ' landing-form-label__locked' : ''?>"<?= $isLocked ? '' : 'data-form-row-hidden'?>>
								<?php $template->showField($gaCounterFields['GACOUNTER_USE'], ['title' => true]);?>
								<?php
								if ($isLocked)
								{
									echo Restriction\Manager::getLockIcon(
										Restriction\Hook::getRestrictionCodeByHookCode('GACOUNTER'),
										[$fieldId]
									);
								}
								?>
							</div>
							<div class="ui-form-row-hidden">
								<div class="ui-form-row">
									<?php $template->showField($gaCounterFields['GACOUNTER_COUNTER']); ?>
									<div class="ui-form-label landing-form-gacounter-send-js" data-form-row-hidden>
										<label class="ui-ctl ui-ctl-checkbox">
											<input type="checkbox" class="ui-ctl-element">
											<div class="ui-ctl-label-text">
												<?=$gaCounterFields['GACOUNTER_SEND_CLICK']->getLabel()?>
											</div>
										</label>
									</div>
									<div class="ui-form-row-hidden">
										<?php $template->showField($gaCounterFields['GACOUNTER_CLICK_TYPE']); ?>
									</div>
									<?php $template->showField($gaCounterFields['GACOUNTER_SEND_SHOW'], ['title' => true]); ?>
								</div>
							</div>
						</div>
						<?php
						$template->showFieldWithToggle('GTM', ['restrictionCode' => 'GACOUNTER']);
						if ($availableOnlyForZoneRu)
						{
							$template->showFieldWithToggle('YACOUNTER', ['restrictionCode' => 'GACOUNTER']);
						}
						?>
						<script>
							BX.ready(function() {
								new BX.Landing.ExternalMetrika(
									BX('<?=$template->getFieldId('GACOUNTER_USE')?>'),
									BX('<?=$template->getFieldId('GACOUNTER_SEND_CLICK')?>'),
									BX('<?=$template->getFieldId('GACOUNTER_SEND_SHOW')?>')
								);
							});
						</script>
					</div>
				</div>
			<?php endif;?>

			<!--Pixels-->
			<?php if (isset($hooks['PIXELFB'], $hooks['PIXELVK'])): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="pixel">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_HOOK_PIXEL') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
						$zone = '';
						if (Loader::includeModule('bitrix24'))
						{
							$zone = \CBitrix24::getPortalZone();
						}
						elseif (
							file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/lang/ru")
							&& !file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/lang/ua")
						)
						{
							$zone = 'ru';
						}
						if ($zone !== 'ru')
						{
							$template->showFieldWithToggle('PIXELFB');
						}

						if ($availableOnlyForZoneRu)
						{
							$template->showFieldWithToggle('PIXELVK');
						}
						?>
					</div>
				</div>
			<?php endif;?>

			<!--Google maps-->
			<?php if (isset($hooks['GMAP']) || isset($hooks['YMAP'])): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="map_required_key">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_HOOK_GMAP') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php $template->showFieldWithToggle('GMAP'); ?>
						<?php
						if ($availableOnlyForZoneRu)
						{
							$template->showFieldWithToggle('YMAP');
						}
						?>
					</div>
				</div>
			<?php endif;?>

			<!--View-->
			<?php if (isset($hooks['VIEW'])): ?>
				<?php $viewFields = $hooks['VIEW']->getPageFields(); ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="view">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['VIEW']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-row">
							<div class="ui-form-label" data-form-row-hidden>
								<?php $template->showField($viewFields['VIEW_USE'], [
									'title' => Loc::getMessage('LANDING_TPL_HOOK_VIEW_USE')
								]);?>
							</div>
							<div class="ui-form-row-hidden">
								<?php if (isset($viewFields['VIEW_TYPE'])): ?>
									<?php
									$value = $viewFields['VIEW_TYPE']->getValue();
									$items = $hooks['VIEW']->getItems();
									if (!$value)
									{
										$itemsKeys = array_keys($items);
										$value = array_shift($itemsKeys);
									}
									?>
									<div class="landing-form-type-page-wrap">
										<?php foreach ($items as $key => $title):?>
											<span class="landing-form-type-page landing-form-type-<?= $key?>">
												<input type="radio" <?php
												?>name="fields[ADDITIONAL_FIELDS][VIEW_TYPE]" <?php
												?>class="ui-radio" <?php
												?>id="<?= $template->getFieldId('VIEW_TYPE_' . $key) ?>" <?php
												?><?php if ($value === $key){?> checked="checked"<?}?> <?php
												?>value="<?= $key ?>" />
												<label for="<?= $template->getFieldId('VIEW_TYPE_' . $key) ?>">
													<span class="landing-form-type-page-img"></span>
													<span class="landing-form-type-page-title"><?= $title?></span>
												</label>
											</span>
										<?php endforeach;?>
									</div>
								<?php endif;?>
							</div>
						</div>
					</div>
				</div>
			<?php endif;?>

			<!--Template layout-->
			<?php if ($arResult['TEMPLATES']): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="layout">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_LAYOUT') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
						$saveRefs = '';
						if (isset($arResult['TEMPLATES'][$row['TPL_ID']['CURRENT']]))
						{
							$aCount = $arResult['TEMPLATES'][$row['TPL_ID']['CURRENT']]['AREA_COUNT'];
							for ($i = 1; $i <= $aCount; $i++)
							{
								$saveRefs .= $i . ':' . (isset($tplRefs[$i]) ? $tplRefs[$i] : '0') . ',';
							}
						}
						?>
						<input
							type="hidden"
							name="fields[TPL_REF]"
							value="<?= $saveRefs ?>"
							id="<?= $template->getFieldId('LAYOUT_TPLREFS') ?>"
						/>
						<?php
						if (isset($hooks['LAYOUT'])):
							$pageFields = $hooks['LAYOUT']->getPageFields();
							if (isset($pageFields['BREAKPOINT'])): ?>
								<input type="hidden" name="fields[ADDITIONAL_FIELDS][LAYOUT_BREAKPOINT]" />
							<?php endif; ?>
						<?php endif; ?>

						<input
							type="checkbox"
							class="ui-checkbox"
							style="display: none;"
							checked="checked"
						/>
						<div
							class="ui-checkbox-hidden-input-inner landing-form-page-layout"
							id="<?= $template->getFieldId('PAGE_LAYOUT') ?>"
						>
							<div class="landing-form-layout-select">
								<?php foreach (array_values($arResult['TEMPLATES']) as $i => $tpl):?>
									<input <?php
										?>class="layout-switcher <?= $template->getFieldClass('LAYOUT-RADIO_' . ($i + 1)) ?>" <?php
										?>data-layout="<?=$tpl['XML_ID']?>" <?php
										?>type="radio" <?php
										?>name="fields[TPL_ID]" <?php
										?>value="<?=$tpl['ID']?>" <?php
										?>id="<?= $template->getFieldId('LAYOUT-RADIO_' . ($i + 1)) ?>"<?php
										?><?php if ($tpl['ID'] === $row['TPL_ID']['CURRENT']) { ?> checked="checked"<?php } ?>
									>
								<?php endforeach;?>
								<div class="landing-form-list">
									<div class="landing-form-list-container">
										<div class="landing-form-list-inner">
											<?php foreach (array_values($arResult['TEMPLATES']) as $i => $tpl):?>
												<label class="landing-form-layout-item <?
												?><?= (!$row['TPL_ID']['CURRENT'] && $tpl['XML_ID'] == 'empty') ? 'landing-form-layout-item-selected ' : ''?><?
												?>landing-form-layout-item-<?= $tpl['XML_ID'] ?>" <?php
												?>data-block="<?= $tpl['AREA_COUNT'] ?>" <?php
												?>data-layout="<?= $tpl['XML_ID'] ?>" <?php
												?>for="<?= $template->getFieldId('LAYOUT-RADIO_' . ($i + 1)) ?>">
													<div class="landing-form-layout-item-img"></div>
												</label>
											<?php endforeach;?>
										</div>
									</div>
									<div class="landing-form-select-buttons">
										<div class="landing-form-select-prev"></div>
										<div class="landing-form-select-next"></div>
									</div>
								</div>
							</div>
							<div class="landing-form-layout-detail">
									<div class="landing-form-layout-img-container">
										<?php foreach (array_values($arResult['TEMPLATES']) as $i => $tpl):?>
											<div class="landing-form-layout-img landing-form-layout-img-<?= $tpl['XML_ID'] ?>" data-layout="<?= $tpl['XML_ID'] ?>"></div>
										<?php endforeach;?>
									</div>
									<div class="landing-form-layout-block-container"></div>
								</div>
						</div>
					</div>
				</div>

				<script>
					BX.ready(function(){
						new BX.Landing.Layout({
							container: BX('<?= $template->getFieldId('PAGE_LAYOUT') ?>'),
							siteId: '<?= $row['ID']['CURRENT'] ?>',
							landingId: -1,
							type: '<?= $arParams['TYPE'] ?>',
							tplRefs: BX('<?= $template->getFieldId('LAYOUT_TPLREFS') ?>'),
							messages: {
								area: '<?= CUtil::jsEscape(Loc::getMessage('LANDING_TPL_LAYOUT_AREA')) ?>'
							},
							<?php if (isset($arResult['TEMPLATES'][$row['TPL_ID']['CURRENT']])):?>
							areasCount: <?= $arResult['TEMPLATES'][$row['TPL_ID']['CURRENT']]['AREA_COUNT'] ?>,
							current: '<?= $arResult['TEMPLATES'][$row['TPL_ID']['CURRENT']]['XML_ID'] ?>',
							<?php else:?>
							areasCount: 0,
							current: 'empty',
							<?php endif;?>
						});
					});
				</script>
			<?php endif;?>

			<!--Language-->
			<?php if (!$isIntranet && !empty($arResult['LANG_CODES']) && $row['LANG']): ?>
				<div class="ui-form-row ui-form-row-middle-input landing-form-additional-row" data-landing-additional-detail="lang">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $row['LANG']['TITLE'] ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
						$selectParams = [];
						$selectParams['value'] = $row['LANG']['CURRENT'];
						$selectParams['options'] = $arResult['LANG_CODES'];
						if (!$selectParams['value'])
						{
							$selectParams['value'] = LANGUAGE_ID;
						}
						?>
						<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
							<div class="ui-ctl-after ui-ctl-icon-angle "></div>
							<select class="ui-ctl-element" name="fields[LANG]">
								<?php foreach($selectParams['options'] as $code => $lang):?>
								<option
									value="<?= $code ?>"
									<?= ($code === $selectParams['value']) ? 'selected' : '' ?>
								>
									<?= htmlspecialcharsbx($lang)?>
								</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>
			<?php endif;?>

			<!--404-->
			<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="404">
				<div class="ui-form-label">
					<div class="ui-ctl-label-text">
						<?= Loc::getMessage('LANDING_TPL_PAGE_404') ?>
					</div>
				</div>
				<div class="ui-form-content">
					<div class="ui-form-label" data-form-row-hidden>
						<label class="ui-ctl ui-ctl-checkbox">
							<input
								type="checkbox"
								id="<?= $template->getFieldId('404-USE') ?>"
								class="ui-ctl-element"
								<?php if ($row['LANDING_ID_404']['CURRENT']){?> checked="checked"<?}?>
							>
							<div class="ui-ctl-label-text"><?= Loc::getMessage('LANDING_TPL_PAGE_404_USE')?></div>
						</label>
					</div>
					<div class="ui-form-row-hidden">
						<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
							<div class="ui-ctl-after ui-ctl-icon-angle "></div>
							<select
								name="fields[LANDING_ID_404]"
								class="ui-ctl-element"
								id="<?= $template->getFieldId('404-SELECT') ?>"
							>
								<option></option>
								<?php foreach ($arResult['LANDINGS'] as $item): ?>
									<?php if (!$item['IS_AREA']): ?>
										<option
											value="<?= $item['ID']?>"
											<?= ($item['ID'] === $row['LANDING_ID_404']['CURRENT']) ? 'selected' : '' ?>
										>
											<?= htmlspecialcharsbx($item['TITLE'])?>
										</option>
									<?php endif; ?>
								<?php endforeach;?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<!--Robots-->
			<?php if (isset($hooks['ROBOTS']) && !$isSMN): ?>
				<div class="ui-form-row landing-form-additional-row landing-form-row-robots" data-landing-additional-detail="robots">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['ROBOTS']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php $template->showFieldWithToggle('ROBOTS');?>
					</div>
				</div>
			<?php endif;?>

			<!--Speed-->
			<?php if (isset($hooks['SPEED'])): ?>
				<?php $speedFields = $hooks['SPEED']->getPageFields(); ?>
				<div class="ui-form-row landing-form-additional-row landing-form-row-speed" data-landing-additional-detail="speed">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['SPEED']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php
						if (!$speedFields['SPEED_USE_WEBPACK']->getValue())
						{
							$speedFields['SPEED_USE_WEBPACK']->setValue('Y');
						}
						$template->showField($speedFields['SPEED_USE_WEBPACK'], ['title' => true]);

						if (!$speedFields['SPEED_USE_LAZY']->getValue())
						{
							$speedFields['SPEED_USE_LAZY']->setValue('Y');
						}
						$template->showField($speedFields['SPEED_USE_LAZY'], ['title' => true]);
						?>
					</div>
					<script type="text/javascript">
						BX.ready(function()
						{
							BX.Landing.NeedPublicationField([
								'<?= $template->getFieldId('SPEED_USE_LAZY') ?>',
								'<?= $template->getFieldId('SPEED_USE_WEBPACK') ?>',
							]);
						});
					</script>
				</div>
			<?php endif;?>

			<!--Headblock-->
			<?php if (isset($hooks['HEADBLOCK'])): ?>
				<div class="ui-form-row landing-form-additional-row landing-form-row-headblock" data-landing-additional-detail="public_html_disallowed">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['HEADBLOCK']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php $template->showFieldWithToggle('HEADBLOCK', [
							'useTitle' => Loc::getMessage('LANDING_TPL_HOOK_HEADBLOCK_USE'),
						]); ?>
					</div>
				</div>
			<?php endif;?>

			<!--CSS block-->
			<?php if (isset($hooks['CSSBLOCK'])): ?>
				<?php $hookFields = $hooks['CSSBLOCK']->getPageFields(); ?>
				<div class="ui-form-row landing-form-additional-row landing-form-row-cssblock" data-landing-additional-detail="public_html_disallowed">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['CSSBLOCK']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-row">
							<div class="ui-form-label" data-form-row-hidden>
								<?php $template->showField($hookFields['CSSBLOCK_USE'], [
									'title' => Loc::getMessage('LANDING_TPL_HOOK_HEADBLOCK_USE'),
								]);?>
							</div>
							<div class="ui-form-row-hidden">
								<div class="ui-form-row">
									<?php $template->showField($hookFields['CSSBLOCK_CODE']);?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif;?>

			<!--Off page-->
			<?php if (!$isIntranet): ?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="off">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_PAGE_503')?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-label" data-form-row-hidden>
							<label class="ui-ctl ui-ctl-checkbox">
								<input
									type="checkbox"
									id="<?= $template->getFieldId('503-USE') ?>"
									class="ui-ctl-element"
									<?php if ($row['LANDING_ID_503']['CURRENT']){?> checked="checked"<?}?>
								>
								<div class="ui-ctl-label-text" for="<?= $template->getFieldId('503-USE') ?>">
									<?= Loc::getMessage('LANDING_TPL_PAGE_503_USE') ?>
								</div>
							</label>
						</div>
						<div class="ui-form-row-hidden">
							<div class="ui-ctl ui-ctl-after-icon ui-ctl-dropdown">
								<div class="ui-ctl-after ui-ctl-icon-angle"></div>
								<select
									name="fields[LANDING_ID_503]"
									class="ui-ctl-element"
									id="<?= $template->getFieldId('503-SELECT') ?>"
								>
									<option></option>
									<?php foreach ($arResult['LANDINGS'] as $item): ?>
										<?php if (!$item['IS_AREA']): ?>
											<option
												value="<?=$item['ID']?>"
												<?= ($item['ID'] === $row['LANDING_ID_503']['CURRENT']) ? 'selected' : '' ?>
											>
												<?=htmlspecialcharsbx($item['TITLE'])?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<!--cookies-->
			<?php if (isset($hooks['COOKIES'])): ?>
				<?php
				$pageFields = $hooks['COOKIES']->getPageFields();
				$agreementId = isset($pageFields['COOKIES_AGREEMENT_ID'])
					? $pageFields['COOKIES_AGREEMENT_ID']->getValue()
					: 0;
				if (!$agreementId)
				{
					$agreementId = $arResult['COOKIES_AGREEMENT']['ID'];
				}
				?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="cookies">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $hooks['COOKIES']->getTitle() ?>
						</div>
					</div>
					<div class="ui-form-content">
						<div class="ui-form-row">
							<div class="ui-form-label" data-form-row-hidden>
								<?php $template->showField($pageFields['COOKIES_USE'], ['title' => true]); ?>
							</div>
							<div class="ui-form-row-hidden">
								<div class="landing-form-cookies-inner">
									<?php if (isset($pageFields['COOKIES_MODE'])):
										$helpUrl = $pageFields['COOKIES_MODE']->getHelpValue();
										$modeOptions = $pageFields['COOKIES_MODE']->getOptions();
										$selectedValue = $pageFields['COOKIES_MODE']->getValue();
										if (!$selectedValue && $availableOnlyForZoneRu)
										{
											$selectedValue = Cookies::MODE_I;
										}
										?>
										<div class="landing-cookies-work-modes">
											<?php foreach ($modeOptions as $modeKey => $modeTitle):
												if (!$selectedValue)
												{
													$selectedValue = $modeKey;
												}
												?>
												<div class="landing-cookies-work-modes-item">
													<input type="radio" name="fields[ADDITIONAL_FIELDS][COOKIES_MODE]" <?php
													?><?= ($selectedValue == $modeKey) ? ' checked="checked"' : '' ?> <?php
													?>value="<?= $modeKey ?>" <?php
													?>id="radio-cookies-mode-<?= $modeKey ?>" />
													<label for="radio-cookies-mode-<?= $modeKey ?>"><?= $modeTitle ?></label>
												</div>
											<?php endforeach;?>
											<?php
											if ($helpUrl)
											{
												echo $helpUrl;
											}
											?>
										</div>
									<?php endif;?>
									<?php if ($arResult['SITE_INCLUDES_SCRIPT']):?>
										<div class="landing-alert-site-includes-script">
											<?= Loc::getMessage('LANDING_TPL_HOOK_COOKIES_SCRIPT_WARN') ?>
										</div>
									<?php endif;?>
									<?$APPLICATION->IncludeComponent(
										'bitrix:landing.userconsent.selector',
										'',
										array(
											'ID' => $agreementId,
											'INPUT_NAME' => 'fields[ADDITIONAL_FIELDS][COOKIES_AGREEMENT_ID]'
										)
									);?>
								</div>
								<a href="<?= $uriCookies->getUri() ?>" class="landing-frame-btn landing-frame-btn-cookies">
									<?= Loc::getMessage('LANDING_TPL_HOOK_COOKIES_EDIT_DESCRIPTIONS') ?>
								</a>
								<div class="landing-form-cookies-settings-wrapper">
									<div class="landing-form-cookies-title"><?= Loc::getMessage('LANDING_TPL_HOOK_COOKIES_VIEW') ?></div>
									<div class="landing-form-cookies-settings">
										<div class="landing-form-cookies-settings-preview">
											<div class="landing-form-cookies-settings-type landing-form-cookies-settings-type-simple">
												<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#FFF" class="landing-form-cookies-settings-preview-svg">
													<path fill-rule="evenodd" d="M7.328.07c.463 0 .917.043 1.356.125.21.04.3.289.228.49a1.5 1.5 0 001.27 1.99h.001a.22.22 0 01.213.243 3.218 3.218 0 003.837 3.453c.18-.035.365.078.384.26A7.328 7.328 0 117.329.07zm.263 10.054a1.427 1.427 0 100 2.854 1.427 1.427 0 000-2.854zM3.697 7.792a.884.884 0 100 1.769.884.884 0 000-1.769zm5.476-.488a.884.884 0 100 1.768.884.884 0 000-1.768zM5.806 3.628a1.427 1.427 0 100 2.854 1.427 1.427 0 000-2.854z"/>
												</svg>
											</div>
											<div class="landing-form-cookies-settings-type landing-form-cookies-settings-type-advanced">
												<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="#FFF" class="landing-form-cookies-settings-preview-svg">
													<path fill-rule="evenodd" d="M7.328.07c.463 0 .917.043 1.356.125.21.04.3.289.228.49a1.5 1.5 0 001.27 1.99h.001a.22.22 0 01.213.243 3.218 3.218 0 003.837 3.453c.18-.035.365.078.384.26A7.328 7.328 0 117.329.07zm.263 10.054a1.427 1.427 0 100 2.854 1.427 1.427 0 000-2.854zM3.697 7.792a.884.884 0 100 1.769.884.884 0 000-1.769zm5.476-.488a.884.884 0 100 1.768.884.884 0 000-1.768zM5.806 3.628a1.427 1.427 0 100 2.854 1.427 1.427 0 000-2.854z"/>
												</svg>
												<span class="landing-form-cookies-settings-preview-text">Cookies</span>
											</div>
										</div>
										<?php if (isset($pageFields['COOKIES_COLOR_BG'])):?>
											<span class="landing-form-cookies-settings-label"><?= $pageFields['COOKIES_COLOR_BG']->getLabel() ?></span>
											<?$pageFields['COOKIES_COLOR_BG']->viewForm([
												'class' => 'landing-form-cookies-color landing-form-cookies-color-bg',
												'name_format' => 'fields[ADDITIONAL_FIELDS][#field_code#]'
											]);?>
										<?php endif;?>
										<?php if (isset($pageFields['COOKIES_COLOR_TEXT'])):?>
											<span class="landing-form-cookies-settings-label"><?= $pageFields['COOKIES_COLOR_TEXT']->getLabel() ?></span>
											<?$pageFields['COOKIES_COLOR_TEXT']->viewForm([
												'class' => 'landing-form-cookies-color landing-form-cookies-color-text',
												'name_format' => 'fields[ADDITIONAL_FIELDS][#field_code#]'
											]);?>
										<?php endif;?>
									</div>
									<div class="landing-form-cookies-position">
										<?php if (isset($pageFields['COOKIES_POSITION'])):?>
											<div class="landing-form-cookies-position-title"><?= $pageFields['COOKIES_POSITION']->getLabel() ?></div>
											<div class="landing-form-cookies-position-inner">
												<input class="landing-form-cookies-position-input" type="radio" name="fields[ADDITIONAL_FIELDS][COOKIES_POSITION]"
													<?= $pageFields['COOKIES_POSITION'] == 'bottom_left' ? ' checked' : '' ?> value="bottom_left" id="bottom_left">
												<input class="landing-form-cookies-position-input" type="radio" name="fields[ADDITIONAL_FIELDS][COOKIES_POSITION]"
													<?= $pageFields['COOKIES_POSITION'] == 'bottom_right' ? ' checked' : '' ?> value="bottom_right" id="bottom_right">
												<div class="landing-form-cookies-position-list">
													<div class="landing-form-cookies-position-list-inner">
														<label class="landing-form-cookies-position-item landing-form-cookies-position-item-left
																	<?= $pageFields['COOKIES_POSITION'] == 'bottom_left' ? ' landing-form-cookies-position-item-selected' : '' ?>" for="bottom_left">
															<div class="landing-form-cookies-position-item-img"></div>
														</label>
														<label class="landing-form-cookies-position-item landing-form-cookies-position-item-right
																	<?= $pageFields['COOKIES_POSITION'] == 'bottom_right' ? ' landing-form-cookies-position-item-selected' : '' ?>" for="bottom_right">
															<div class="landing-form-cookies-position-item-img"></div>
														</label>
													</div>
												</div>
											</div>
										<?php endif;?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif;?>

			<!--Copyright-->
			<?php if (isset($hooks['COPYRIGHT'])): ?>
				<?php
				$pageFields = $hooks['COPYRIGHT']->getPageFields();
				if (
					!$pageFields['COPYRIGHT_SHOW']->getValue() ||
					$hooks['COPYRIGHT']->isLocked()
				)
				{
					$pageFields['COPYRIGHT_SHOW']->setValue('Y');
				}
				?>
				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="sign">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= $pageFields['COPYRIGHT_SHOW']->getLabel() ?>
						</div>
					</div>
					<div class="ui-form-content landing-form-content-copyright">
						<?php $template->showField($pageFields['COPYRIGHT_SHOW'], [
							'title' => Loc::getMessage('LANDING_TPL_ACTION_SHOW')
						]);?>
						<?php
						if ($hooks['COPYRIGHT']->isLocked())
						{
							echo Restriction\Manager::getLockIcon(
								Restriction\Hook::getRestrictionCodeByHookCode('COPYRIGHT'),
								[$template->getFieldId('COPYRIGHT_SHOW')]
							);
						}
						?>
					</div>
				</div>
			<?php endif;?>

			<?php if ($arResult['SHOW_RIGHTS']): ?>
				<?php
					$tasksStr = '<div class="ui-ctl ui-ctl-multiple-select">';
					$tasksStr.= '<select name="fields[RIGHTS][TASK_ID][#inc#][]" multiple="multiple" size="7" class="ui-ctl-element">';
					foreach ($arResult['ACCESS_TASKS'] as $task)
					{
						$tasksStr .= '<option value="' . $task['ID'] . '">' .
							htmlspecialcharsbx('['.$task['ID'].'] '.$task['TITLE']) .
							'</option>';
					}
					$tasksStr .= '</select></div>';
					$accessCodes = [];
				?>

				<div class="ui-form-row landing-form-additional-row" data-landing-additional-detail="access">
					<div class="ui-form-label">
						<div class="ui-ctl-label-text">
							<?= Loc::getMessage('LANDING_TPL_HOOK_RIGHTS_LABEL') ?>
						</div>
					</div>
					<div class="ui-form-content">
						<?php if (Manager::checkFeature(Manager::FEATURE_PERMISSIONS_AVAILABLE)):?>
							<table
								id="<?= $template->getFieldId('RIGHTS_TABLE') ?>"
								class="internal"
								width="100%"
								align="center"
							>
								<tbody>
								<?php foreach ($arResult['CURRENT_RIGHTS'] as $i => $right):
									$code = $right['ACCESS_CODE'];
									$accessCodes[] = $code;
									?>
									<tr class="landing-form-rights">
										<td class="landing-form-rights-right">
											<?= $right['ACCESS_PROVIDER'] ? htmlspecialcharsbx($right['ACCESS_PROVIDER']) . ': ' : '' ?>
											<?= htmlspecialcharsbx($right['ACCESS_NAME']) ?>:
										</td>
										<td class="landing-form-rights-left">
											<div class="ui-ctl ui-ctl-multiple-select">
												<select
													name="fields[RIGHTS][TASK_ID][<?= $i ?>][]"
													multiple="multiple"
													size="7"
													class="ui-ctl-element"
												>
													<?php foreach ($arResult['ACCESS_TASKS'] as $accessTask):?>
														<option value="<?= $accessTask['ID'] ?>"<?if (in_array($accessTask['ID'], $right['TASK_ID'])){?> selected="selected"<?}?>>
															<?= htmlspecialcharsbx('[' . $accessTask['ID']. ']' . $accessTask['TITLE']) ?>
														</option>
													<?php endforeach;?>
												</select>
											</div>

											<input type="hidden" name="fields[RIGHTS][ACCESS_CODE][]" value="<?= htmlspecialcharsbx($code) ?>">
											<a href="javascript:void(0);" onclick="deleteAccessRow(this);" data-id="<?= htmlspecialcharsbx($code) ?>" class="landing-form-rights-delete"></a>
										</td>
									</tr>
								<?php endforeach;?>
								<tr>
									<td>
										<a href="javascript:void(0)" id="<?= $template->getFieldId('RIGHTS_FORM') ?>">
											<?= Loc::getMessage('LANDING_TPL_HOOK_RIGHTS_LABEL_NEW') ?>
										</a>
									</td>
								</tr>
								</tbody>
							</table>
						<?php else:?>
							<?= Loc::getMessage('LANDING_TPL_HOOK_RIGHTS_PROMO_SALE') ?>
						<?php endif;?>
					</div>
				</div>

				<script>
					BX.ready(function() {
						new BX.Landing.Access({
							table: BX('<?= $template->getFieldId('RIGHTS_TABLE') ?>'),
							form: BX('<?= $template->getFieldId('RIGHTS_FORM') ?>'),
							select: '<?= CUtil::jsEscape($tasksStr) ?>',
							inc: <?= count($arResult['CURRENT_RIGHTS']) ?>,
						});
					});
				</script>
			<?php endif;?>
		</div>

		<!--BUTTONS-->
		<?php
		// for complex component landing.settings not need buttons. If isAjax will be incorrect - need add other flag for landgin.settings
		if (!$isAjax)
		{
			$buttonSave = [
				'TYPE' => 'save',
				'ID' => 'landing-save-btn',
				'NAME' => 'submit',
				'CAPTION' => Loc::getMessage('LANDING_TPL_BUTTON_' . ($arParams['SITE_ID'] ? 'SAVE' : 'ADD')),
				'VALUE' => Loc::getMessage('LANDING_TPL_BUTTON_' . ($arParams['SITE_ID'] ? 'SAVE' : 'ADD')),
			];
			$buttonCancel = [
				'TYPE' => 'cancel',
				'CAPTION' => Loc::getMessage('LANDING_TPL_BUTTON_CANCEL'),
				'LINK' => $arParams['PAGE_URL_LANDINGS'],
			];
			if ($request->get('IFRAME') === 'Y')
			{
				$buttonCancel['ID'] = $template->getFieldId('ACTION_CLOSE');
				$buttonCancel['LINK'] = '#';
			}
			$APPLICATION->IncludeComponent(
				'bitrix:ui.button.panel',
				'',
				['BUTTONS' => [$buttonSave, $buttonCancel]]
			);
		}
		?>

	</form>
</div>

<script type="text/javascript">
	<?php if (isset($accessCodes)):?>
	var landingAccessSelected = <?= json_encode(array_fill_keys($accessCodes, true)) ?>;
	<?php endif;?>
	BX.ready(function(){
		new BX.Landing.EditTitleForm(BX('<?= $template->getFieldId('EDITABLE_TITLE') ?>'), 600, true);
		new BX.Landing.Favicon();
		new BX.Landing.Custom404And503(
			BX('<?= $template->getFieldId('404-SELECT') ?>'),
			BX('<?= $template->getFieldId('404-USE') ?>')
		);
		new BX.Landing.Custom404And503(
			BX('<?= $template->getFieldId('503-SELECT') ?>'),
			BX('<?= $template->getFieldId('503-USE') ?>')
		);
		new BX.Landing.Copyright(BX('landing-site-set-form'), BX('<?= $template->getFieldId('COPYRIGHT_SHOW') ?>'));
		new BX.Landing.ToggleAdditionalFields(BX('landing-site-set-form'));
		new BX.UI.LayoutForm({container: BX('landing-site-set-form')});
		<?php if (isset($hooks['COOKIES'])):?>
		new BX.Landing.Cookies();
		<?php endif;?>
	});

</script>
