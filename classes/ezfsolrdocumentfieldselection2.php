<?php
/**
 * Helper class to index multiple-choice-selection attributes
 * We want to
 * - allow the end users to be able to search using the names of selection items
 * - have a working faceting schema even if selection items contains spaces (eg: english spoken)
 *
 * @author
 * @version $Id$
 * @copyright (C) 2011
 */
 
class ezfSolrDocumentFieldSelection2 extends ezfSolrDocumentFieldBase
{
    /**
    * Use a specific field name to allow faceting: a string that does not tokenize
    */
    public static function getFieldName( eZContentClassAttribute $classAttribute, $subAttribute = null, $context = 'search' )
    {
        if ( $context == 'facet' )
        {
            return self::ATTR_FIELD_PREFIX . $classAttribute->attribute( 'identifier' ) . '____ms';
        }
        return self::ATTR_FIELD_PREFIX . $classAttribute->attribute( 'identifier' ) . '_cis';
 
    }
 
    /**
    * Index data twice: once in a facetable filed, one in a searchable field
    */
    public function getData()
    {
        //logic taken from eZSelectionType::toString
        $contentClassAttribute = $this->ContentObjectAttribute->attribute( 'contentclass_attribute' );
        $field = self::getFieldName( $contentClassAttribute );
        $ffield = self::getFieldName( $contentClassAttribute, 'null', 'facet' );
 
        $selected = ezSelectiontype::objectAttributeContent( $this->ContentObjectAttribute );
        if ( count( $selected ) )
        {
            $returnData = array();
            $classContent = ezSelectiontype::classAttributeContent( $this->ContentObjectAttribute->attribute( 'contentclass_attribute' ) );
            $optionArray = $classContent['options'];
            foreach ( $selected as $id )
            {
                foreach ( $optionArray as $option )
                {
                    $optionID = $option['id'];
                    if ( $optionID == $id )
                    {
                        $returnData[] = $option['name'];
                        break;
                    }
                }
                /// @todo add warning if any unknown ids left
            }
            return array( $ffield => $returnData, $field => implode( $returnData, ' ' ) );
        }
        return array( $field => '', $ffield => array() );
    }
}
 
?>