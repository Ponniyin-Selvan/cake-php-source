<?php
/**
 * File containing the ezcMailPop3TransportOptions class
 *
 * @package Mail
 * @version 1.3
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the options for POP3 transport.
 *
 * The options from ezcMailTransportOptions are inherited.
 *
 * @property int $authenticationMethod
 *           Specifies the method to connect to the POP3 transport. The methods
 *           supported are {@link ezcMailPop3Transport::AUTH_PLAIN_TEXT} and
 *           {@link ezcMailPop3Transport::AUTH_APOP}.
 *
 * @package Mail
 * @version 1.3
 */
class ezcMailPop3TransportOptions extends ezcMailTransportOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        // default authentication method is PLAIN
        $this->authenticationMethod = ezcMailPop3Transport::AUTH_PLAIN_TEXT;

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'authenticationMethod':
                if ( !is_numeric( $value ) ) 
                {
                    throw new ezcBaseValueException( $name, $value, 'int' );
                }
                $this->properties[$name] = (int) $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }
}
?>
