<?php
require_once('select-field.class.php');

class multiSelectField extends selectField
{
   const IS_MULTIPLE    = true;

   public function isValid($value)
   {
      // If the field is required it can't be empty
      if ($this->isRequired() && empty($value)) {
         Session::addMessageAfterRedirect(__('A required field is empty:', 'formcreator') . ' ' . $this->getLabel(), false, ERROR);
         return false;

      // Min range not set or number of selected item lower than min
      } elseif (!empty($this->fields['range_min']) && (count($value) - 1 < $this->fields['range_min'])) {
         $message = sprintf(__('The following question needs of at least %d answers', 'formcreator'), $this->fields['range_min']);
         Session::addMessageAfterRedirect($message . ' ' . $this->getLabel(), false, ERROR);
         return false;

      // Max range not set or number of selected item greater than max
      } elseif (!empty($this->fields['range_max']) && (count($value) - 1 > $this->fields['range_max'])) {
         $message = sprintf(__('The following question does not accept more than %d answers', 'formcreator'), $this->fields['range_max']);
         Session::addMessageAfterRedirect($message . ' ' . $this->getLabel(), false, ERROR);
         return false;
      }

      // All is OK
      return true;
   }

   public function displayField($canEdit = true)
   {
      if ($canEdit) {
         parent::displayField($canEdit);
      } else {
         echo empty($this->getAnswer()) ? '' :  implode(', ', json_decode($this->getAnswer()));
      }
   }

   public function getAnswer()
   {
      $return = array();
      $values = $this->getAvailableValues();

      if (empty($this->getValue())) return '';

      $tab_values = is_array($this->getValue()) ? $this->getValue() : json_decode($this->getValue());

      foreach ($tab_values as $value) {
         if (isset($values[$value])) $return[] = $value;
      }
      return json_encode($return);
   }

   public static function getName()
   {
      return __('Multiselect', 'formcreator');
   }

   public static function getPrefs()
   {
      return array(
         'required'       => 1,
         'default_values' => 1,
         'values'         => 1,
         'range'          => 1,
         'show_empty'     => 0,
         'regex'          => 0,
         'show_type'      => 1,
         'dropdown_value' => 0,
         'glpi_objects'   => 0,
         'ldap_values'    => 0,
      );
   }

   public static function getJSFields()
   {
      $prefs = self::getPrefs();
      return "tab_fields_fields['multiselect'] = 'showFields(" . implode(', ', $prefs) . ");';";
   }
}
