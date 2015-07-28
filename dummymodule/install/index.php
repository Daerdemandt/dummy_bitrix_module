<?

# TODO: use all text through GetMessage, to be language-indifferent
# TODO: change class name (can it be done automatically?).
# Note that class name must be similar to module name (more info at https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=3216 )

class dummymodule extends CModule
{
	var $MODULE_ID; # Automatically set to be folder's name
	var $MODULE_VERSION; # set in ./version.php
	var $MODULE_VERSION_DATE; # set in ./version.php
	#TODO
	var $MODULE_NAME = 'Просто тестовый модуль';
	#TODO
	var $MODULE_DESCRIPTION = 'После установки вы сможете пользоваться какими-то компонентами';
	var $MODULE_CSS;

	var $INSTALLATION_FOLDER; # if not set, /local is assumed
	var $COMPONENTS_INSTALLATION_FOLDER; #if not set, $INSTALLATION_FOLDER/components is assumed
	var $TEMPLATES_INSTALLATION_FOLDER; #if not set, $INSTALLATION_FOLDER/templates is assumed
	var $PHP_INTERFACE_INSTALLATION_FOLDER; #if not set, $INSTALLATION_FOLDER/php_interface is assumed


	function __construct() {
		$PathDivided = explode('/', __DIR__);
		end($PathDivided);
		$this->MODULE_ID = prev($PathDivided); # name of folder in .../modules/

		$arModuleVersion = array();

		include(__DIR__ . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		if (!$this->INSTALLATION_FOLDER) {
			$this->INSTALLATION_FOLDER = '/local';
		}
		if (!$this->COMPONENTS_INSTALLATION_FOLDER) {
			$this->COMPONENTS_INSTALLATION_FOLDER = $this->INSTALLATION_FOLDER . '/components';
		}
		if (!$this->TEMPLATES_INSTALLATION_FOLDER) {
			$this->TEMPLATES_INSTALLATION_FOLDER = $this->INSTALLATION_FOLDER . '/templates';
		}
		if (!$this->PHP_INTERFACE_INSTALLATION_FOLDER) {
			$this->PHP_INTERFACE_INSTALLATION_FOLDER = $this->INSTALLATION_FOLDER . '/php_interface';
		}
	}

	function InstallFiles($arParams = array()) {
		# TODO Shouldn't this be done with __DIR__?
		# TODO: do not ignore results of CopyDirFiles
		# Components:
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/components',
			$_SERVER['DOCUMENT_ROOT'] . $this->COMPONENTS_INSTALLATION_FOLDER, true, true);
		# Templates:
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/templates',
			$_SERVER['DOCUMENT_ROOT'] . $this->TEMPLATES_INSTALLATION_FOLDER, true, true);
		# php_interface:
		CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/php_interface',
			$_SERVER['DOCUMENT_ROOT'] . $this->PHP_INTERFACE_INSTALLATION_FOLDER, true, true);
		return true;
	}

	function UnInstallFiles() {
		# TODO: check if any files have been changed
		# TODO: save changed files to a separate folder (make it optional)
		# TODO: do not ignore result of DeleteDirFiles
		# TODO: make it automatically delete from installation folders all files that belong to this module
		DeleteDirFilesEx("/bitrix/components/test_module_component");
		return true;
	}

	function InstallDB() {
		return true;
	}


	function UninstallDB() {
		# TODO: if new tables in DB were created or sth like that, backup these changes (make it optional)
		return true;
	}


	function InstallEvents() {
		return true;
	}

	function UnInstallEvents() {
		return true;
	}

	function DoInstall() {
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallDB();
		$this->InstallFiles();
		$this->InstallEvents();
		RegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile("Установка модуля $this->MODULE_ID", $DOCUMENT_ROOT . "/bitrix/modules/$this->MODULE_ID/install/step.php");
	}

	function DoUninstall() {
		global $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallDB();
		$this->UninstallEvents();
		$this->UnInstallFiles();
		UnRegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile("Деинсталляция модуля $this->MODULE_ID", $DOCUMENT_ROOT . "/bitrix/modules/$this->MODULE_ID/install/unstep.php");
	}
}
