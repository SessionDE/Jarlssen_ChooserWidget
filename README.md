Jarlssen_ChooserWidget
======================

Magento extension that gives the ability to create Product, Category, CMS Page and Static Block choosers in generic admin forms.

Here you can read my original blog post about the extension: http://www.jarlssen.de/blog/2014/04/24/magento-chooser-widget-add-edit-admin-forms

There are 4 important things you need to do to make the chooser work:

It's required to add in the layout update a handle called "editor". This handle includes the JS logic that is needed to render the chooser popups

After that in method _prepareForm() of your admin form you need to have an instance of helper 'jarlssen_chooser_widget/chooser' and pass some parameters to the function creating the chooser.

Use any of the chooser create functions of Jarlssen_ChooserWidget_Helper_Chooser:
 * createProductChooser
 * createCategoryChooser
 * createCmsPageChooser
 * createCmsBlockChooser
 * createChooser

There is a required config value called "input_name" and must be passed to the chooser through a configuration array.

The function creating the chooser accepts the following parameters:
 * $model - instance of Mage_Core_Model_Abstract - the current entity
 * $fieldset - instance of Varien_Data_Form_Element_Fieldset - It’s required, because we create the chooser in this fieldset
 * $config - array of the widget configuration, the element “input_name” is required, because this is the name of the field name, that we save after form submit.
 * $blockAlias - this parameter is used only when we invoke Jarlssen_ChooserWidget_Helper_Chooser::createChooser and it’s useful for creating our own custom chooser

The array $config also can contain more elements, but they are not mandatory:
 * 'input_label' - The text of the input label
 * 'button_text' - The text of the chooser button
 * 'required' - If it’s true, then we will have frontend validation and to pass it we need to choose something from the chooser

Example of config array:

```php
$categoryConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('Product'),
    'button_text' => $this->__('Select Product...'),
    'required'    => true
);
```

*Code Examples*

Product Chooser:

```php
$chooserHelper = Mage::helper('jarlssen_chooser_widget/chooser');
 
$productConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('Product'),
    'button_text' => $this->__('Select Product...'),
    'required'    => true
);
 
$chooserHelper->createCategoryChooser($model, $fieldset, $productConfig);
```

Category Chooser:
```php
$chooserHelper = Mage::helper('jarlssen_chooser_widget/chooser');

$categoryConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('Category'),
    'button_text' => $this->__('Select Category...'),
    'required'    => true
);

$chooserHelper->createProductChooser($model, $fieldset, $categoryConfig);
```

Static Block Chooser:
```php
$chooserHelper = Mage::helper('jarlssen_chooser_widget/chooser');

$blockConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('Block'),
    'button_text' => $this->__('Select Block...'),
    'required'    => true
);

$chooserHelper->createCmsBlockChooser($model, $fieldset, $blockConfig);
```

Example for CMS Page Chooser:
```php
$chooserHelper = Mage::helper('jarlssen_chooser_widget/chooser');

$cmsPageConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('CMS Page'),
    'button_text' => $this->__('Select CMS Page…'),
    'required'    => true
);

$chooserHelper->createCmsPageChooser($model, $fieldset, $cmsPageConfig);
```

Example for Custom Chooser:
```php
$chooserHelper = Mage::helper('jarlssen_chooser_widget/chooser');

$customChooserConfig = array(
    'input_name'  => 'entity_link',
    'input_label' => $this->__('Custom entity'),
    'button_text' => $this->__('Select entity…'),
    'required'    => true
);

$chooserBlock = 'custom_module/chooser';

$chooserHelper->createChooser($model, $fieldset, $customChooserConfig, $chooserBlock);
```

*Data representation*

| Chooser  | Format | Example |
| ------------- | ------------- | ------------- |
| Product  |  product/{product_id}/{category_id} / *{category_id} is optional* | product/14509 / product/14509/32 |
| Category | category/{category_id} | category/22 |
| CMS Page | {cms_page_id} | 7 |
| Static Block | {static_block_id} | 3 |
| Custom | N/A | N/A  |

