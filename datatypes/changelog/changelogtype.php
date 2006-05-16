<?php

include_once( 'kernel/classes/datatypes/eztext/eztexttype.php' );

define( "EZ_DATATYPESTRING_CHANGELOG", "changelog" );

class ChangelogType extends eZTextType
{
    function ChangelogType()
    {
        $this->eZDataType( EZ_DATATYPESTRING_CHANGELOG, ezi18n( 'kernel/classes/datatypes', "Changelog", 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'text' ) ) );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( &$contentObjectAttribute, $currentVersion, &$originalContentObjectAttribute )
    {
        eZDebug::writeDebug( $currentVersion, 'changelog::initializeObjectAttribute() current version' );

        if ( $currentVersion != false )
        {
            if ( $originalContentObjectAttribute->attribute( 'id' ) == $contentObjectAttribute->attribute( 'id' ) )
            {
                // new version of an existing language
                include_once( 'kernel/classes/ezcontentobjectversion.php' );

                $objectVersion = eZContentObjectVersion::fetchVersion( $originalContentObjectAttribute->attribute( 'version' ), $originalContentObjectAttribute->attribute( 'contentobject_id' ) );

                $newObjectVersion =& $contentObjectAttribute->attribute( 'object_version' );
                if ( $contentObjectAttribute->attribute( 'language_id' ) != $newObjectVersion->attribute( 'initial_language_id' ) )
                {
                    // not the edited language
                    eZDebug::writeDebug( 'not the edited language', 'changelog::initializeObjectAttribute()' );
                    $dataText = ezi18n( 'kernel/classes/datatypes', '- copy of %version', '', array( '%version' => $originalContentObjectAttribute->attribute( 'version' ) ) );
                }
                else
                {
                    // the edited language
                    eZDebug::writeDebug( 'the edited language', 'changelog::initializeObjectAttribute()' );
                    eZDebug::writeDebug( $objectVersion->attribute( 'status' ), 'changelog::initializeObjectAttribute() original version status' );

                    $dataText = ezi18n( 'kernel/classes/datatypes', '- based on version %version', '', array( '%version' => $originalContentObjectAttribute->attribute( 'version' ) ) );

                    $doNotCopyChangelog = array( EZ_VERSION_STATUS_PUBLISHED, 
                                                 EZ_VERSION_STATUS_ARCHIVED );

                    if ( !in_array( $objectVersion->attribute( 'status' ), $doNotCopyChangelog ) )
                    {
                        $dataText = $dataText . "\r\n" . $originalContentObjectAttribute->attribute( "data_text" );
                    }
                }

                $contentObjectAttribute->setAttribute( "data_text", $dataText );
            }
            else
            {
                if ( $originalContentObjectAttribute->attribute( 'contentobject_id' ) == $contentObjectAttribute->attribute( 'contentobject_id' ) )
                {
                    // translation to a new language
                    eZDebug::writeDebug( 'translation to a new language', 'changelog::initializeObjectAttribute()' );
                    $dataText = ezi18n( 'kernel/classes/datatypes', '- translation based on %version', '', array( '%version' => $originalContentObjectAttribute->attribute( 'version' ) ) );

                    $contentObjectAttribute->setAttribute( "data_text", $dataText );
                }
                else
                {
                    // a copy
                    eZDebug::writeDebug( 'copy of an object', 'changelog::initializeObjectAttribute()' );
                    $dataText = $originalContentObjectAttribute->attribute( "data_text" );
                    $contentObjectAttribute->setAttribute( "data_text", $dataText );
                }
            }
        }

        $contentClassAttribute =& $contentObjectAttribute->contentClassAttribute();
        if ( $contentClassAttribute->attribute( "data_int1" ) == 0 )
        {
            $contentClassAttribute->setAttribute( "data_int1", 10 );
            $contentClassAttribute->store();
        }
    }
    /*!
     \reimp
    */
    function isIndexable()
    {
        return false;
    }

    /*!
     \reimp
    */
    function isInformationCollector()
    {
        return false;
    }

}

eZDataType::register( EZ_DATATYPESTRING_CHANGELOG, "changelogtype" );

?>
