<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Gradiadsense extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'gradiadsense';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Carlos Alzate';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->getTranslator()->trans('Gradi Adsense', array(), 'Modules.gradiadsense.Admin');
        $this->description = $this->getTranslator()->trans('Módulo de prueba Banner gradiAdsense', array(), 'Modules.gradiadsense.Admin');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('GRADIADSENSE_ACTIVO', false);
        Configuration::updateValue('GRADIADSENSE_TITULO', 'Titulo de prueba por defecto');
        Configuration::updateValue('GRADIADSENSE_DESCRIPCION', 'Describe por defecto...');
        Configuration::updateValue('GRADIADSENSE_CTA_LABEL', 'CTA Label por defecto...');
        Configuration::updateValue('GRADIADSENSE_CTA_URL', '#');
        Configuration::updateValue('GRADIADSENSE_BACKGROUND', 'https://www.gradiweb.com/wp-content/themes/gradiwebtemplate/img/estimate-footer-website.png');

        return parent::install() &&
        $this->registerHook('header') &&
        $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('GRADIADSENSE_ACTIVO');
        Configuration::deleteByName('GRADIADSENSE_TITULO');
        Configuration::deleteByName('GRADIADSENSE_DESCRIPCION');
        Configuration::deleteByName('GRADIADSENSE_CTA_LABEL');
        Configuration::deleteByName('GRADIADSENSE_CTA_URL');
        Configuration::deleteByName('GRADIADSENSE_BACKGROUND');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitGradiadsenseModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGradiadsenseModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Activo?'),
                        'name' => 'GRADIADSENSE_ACTIVO',
                        'is_bool' => true,
                        'desc' => $this->l('Activa/Desactiva módulo'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Si')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('No')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        //'prefix' => 'Texto',
                        'desc' => $this->l('Título de la publicidad'),
                        'name' => 'GRADIADSENSE_TITULO',
                        'label' => $this->l('Título'),
                    ),
                    array(
                        'type' => 'text',
                        //'prefix' => '<i class="icon icon-text"></i>',
                        'desc' => $this->l('descripción corta de la publicidad'),
                        'name' => 'GRADIADSENSE_DESCRIPCION',
                        'label' => $this->l('Descripción'),
                    ),
                    array(
                        'type' => 'text',
                        //'prefix' => '<i class="icon icon-text"></i>',
                        'desc' => $this->l('Label del Call To Action'),
                        'name' => 'GRADIADSENSE_CTA_LABEL',
                        'label' => $this->l('Label CTA'),
                    ),
                    array(
                        'type' => 'text',
                        //'prefix' => '<i class="icon icon-text"></i>',
                        'desc' => $this->l('URL del Call To Action Ej: https://google.com'),
                        'name' => 'GRADIADSENSE_CTA_URL',
                        'label' => $this->l('URL del CTA'),
                    ),
                    array(
                        'type' => 'file',
                        //'prefix' => '<i class="icon icon-text"></i>',
                        'desc' => $this->l('URL Imagen background del banner ej: https://www.gradiweb.com/wp-content/themes/gradiwebtemplate/img/back-service.png'),
                        'name' => 'GRADIADSENSE_BACKGROUND',
                        'label' => $this->l('URL Imagen Background'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'GRADIADSENSE_ACTIVO' => Configuration::get('GRADIADSENSE_ACTIVO', true),
            'GRADIADSENSE_TITULO' => Configuration::get('GRADIADSENSE_TITULO', 'Titulo de prueba por defecto'),
            'GRADIADSENSE_DESCRIPCION' => Configuration::get('GRADIADSENSE_DESCRIPCION', 'Describe por defecto...'),
            'GRADIADSENSE_CTA_LABEL' => Configuration::get('GRADIADSENSE_CTA_LABEL', 'CTA Label por defecto...'),
            'GRADIADSENSE_CTA_URL' => Configuration::get('GRADIADSENSE_CTA_URL', '#'),
            'GRADIADSENSE_BACKGROUND' => Configuration::get('GRADIADSENSE_BACKGROUND', 'https://www.gradiweb.com/wp-content/themes/gradiwebtemplate/img/estimate-footer-website.png'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        //print_r($_FILES['GRADIADSENSE_BACKGROUND']);
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));

            //Cargo img
            $update_images_values = false;
            if (isset($_FILES['GRADIADSENSE_BACKGROUND'])
                && !empty($_FILES['GRADIADSENSE_BACKGROUND']['tmp_name'])) {
                if ($error = ImageManager::validateUpload($_FILES['GRADIADSENSE_BACKGROUND'], 4000000)) {
                    return $error;
                }
                else {
                    $ext = substr($_FILES['GRADIADSENSE_BACKGROUND']['name'], strrpos($_FILES['GRADIADSENSE_BACKGROUND']['name'], '.') + 1);
                    $file_name = md5($_FILES['GRADIADSENSE_BACKGROUND']['name']).'.'.$ext;

                    if(!move_uploaded_file($_FILES['GRADIADSENSE_BACKGROUND']['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name)) {
                        return $this->displayError($this->trans('An error occurred while attempting to upload the file.', array(), 'Admin.Notifications.Error'));
                    }
                    else {
                        if(Configuration::get('GRADIADSENSE_BACKGROUND') != $file_name) {
                            @unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get('GRADIADSENSE_BACKGROUND'));
                        }

                        $values['GRADIADSENSE_BACKGROUND'] = $file_name;
                    }
                }

                $update_images_values = true;
            }

            if ($update_images_values) {
                Configuration::updateValue('GRADIADSENSE_BACKGROUND', $values['GRADIADSENSE_BACKGROUND']);
            }
            //$this->_clearCache($this->templateFile);

        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    /*public function hookDisplayHome()
    {
        //print_r($this->context);
    }*/

    public function renderWidget($hookName, array $configuration){
        //print_r($this->context);
        $this->context->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        $template = "displayHome.tpl";

        return $this->fetch('module:'.$this->name.'/views/templates/hook/'.$template);
    }

    public function getWidgetVariables($hookName, array $configuration){

        $BgImg = 'https://www.gradiweb.com/wp-content/themes/gradiwebtemplate/img/estimate-footer-website.png';//Defecto
        if(Configuration::get('GRADIADSENSE_BACKGROUND') != '')
            $BgImg = $this->_path . 'img/' . Configuration::get('GRADIADSENSE_BACKGROUND');

        return [
            'GRADIADSENSE_ACTIVO' => Configuration::get('GRADIADSENSE_ACTIVO'),
            'GRADIADSENSE_TITULO' => Configuration::get('GRADIADSENSE_TITULO'),
            'GRADIADSENSE_DESCRIPCION' => Configuration::get('GRADIADSENSE_DESCRIPCION'),
            'GRADIADSENSE_CTA_LABEL' => Configuration::get('GRADIADSENSE_CTA_LABEL'),
            'GRADIADSENSE_CTA_URL' => Configuration::get('GRADIADSENSE_CTA_URL'),
            'GRADIADSENSE_BACKGROUND' => $BgImg,
        ];
    }
}
