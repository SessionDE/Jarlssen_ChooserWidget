<?php
class Jarlssen_ChooserWidget_Block_CustomerChooser extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_selectedCustomers = array();

    /**
     * Block construction, prepare grid params
     *
     * @param array $arguments Object data
     */
    public function __construct($arguments=array())
    {
        parent::__construct($arguments);
        $this->setDefaultSort('name');
        $this->setUseAjax(true);
    }

    /**
     * Prepare chooser element HTML
     *
     * @param Varien_Data_Form_Element_Abstract $element Form Element
     * @return Varien_Data_Form_Element_Abstract
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $uniqId = Mage::helper('core')->uniqHash($element->getId());
        $sourceUrl = $this->getUrl('*/jarlssen_chooserWidget_customerChooser/chooser', array(
            'uniq_id' => $uniqId,
            'use_massaction' => false,
        ));

        $chooser = $this->getLayout()->createBlock('widget/adminhtml_widget_chooser')
            ->setElement($element)
            ->setTranslationHelper($this->getTranslationHelper())
            ->setConfig($this->getConfig())
            ->setFieldsetId($this->getFieldsetId())
            ->setSourceUrl($sourceUrl)
            ->setUniqId($uniqId);

        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Checkbox Check JS Callback
     *
     * @return string
     */
    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, element) {
                $(grid.containerId).fire('product:changed', {element: element});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var customerId = trElement.down("td").innerHTML;
                    var customerName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = customerName;
                    var optionValue = customerId;
                    '.$chooserJsObject.'.setElementValue(optionValue);
                    '.$chooserJsObject.'.setElementLabel(optionLabel);
                    '.$chooserJsObject.'.close();
                }
            ';
        }

        return '';
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_customers') {
            $selected = $this->getSelectedProducts();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$selected));
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$selected));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare products collection, defined collection filters (category, product type)
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $collection = Mage::getResourceModel('customer/customer_collection')
	        ->addAttributeToSelect('firstname')
	        ->addAttributeToSelect('lastname')
	        ->addNameToSelect();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for products grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn('in_customers', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_customers',
                'inline_css' => 'checkbox entities',
                'field_name' => 'in_customers',
                'values'    => $this->getSelectedCustomers(),
                'align'     => 'center',
                'index'     => 'entity_id',
                'use_index' => true,
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('customer')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('chooser_sku', array(
            'header'    => Mage::helper('customer')->__('E-mail'),
            'name'      => 'chooser_email',
            'width'     => '80px',
            'index'     => 'email'
        ));
        $this->addColumn('chooser_name', array(
            'header'    => Mage::helper('catalog')->__('Customer Name'),
            'name'      => 'chooser_name',
            'index'     => 'name'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/jarlssen_chooserWidget_customerChooser/chooser', array(
            '_current' => true,
            'uniq_id' => $this->getId(),
            'use_massaction' => $this->getUseMassaction(),
        ));
    }

    /**
     * Setter
     *
     * @param array $selectedCustomers
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Widget_Chooser
     */
    public function setSelectedCustomers($selectedCustomers)
    {
        $this->_selectedCustomers = $selectedCustomers;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedCustomers()
    {
        if ($selectedCustomers = $this->getRequest()->getParam('selected_customers', null)) {
            $this->setSelectedCustomers($selectedCustomers);
        }
        return $this->_selectedCustomers;
    }
}
