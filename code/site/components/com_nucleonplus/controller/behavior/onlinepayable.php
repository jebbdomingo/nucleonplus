<?php
/**
 * Nucleon Plus
 *
 * @package     Nucleon Plus
 * @copyright   Copyright (C) 2015 - 2020 Nucleon Plus Co. (http://www.nucleonplus.com)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        https://github.com/jebbdomingo/nucleonplus for the canonical source repository
 */

class ComNucleonplusControllerBehaviorOnlinepayable extends KControllerBehaviorAbstract
{
    /**
     *
     * @var array
     */
    protected $_reference_column;

    /**
     *
     * @var array
     */
    protected $_actions;

    /**
     * Constructor.
     *
     * @param KObjectConfig $config Configuration options.
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_reference_column = KObjectConfig::unbox($config->reference_column);
        $this->_actions          = KObjectConfig::unbox($config->actions);
    }

    /**
     * Initializes the options for the object.
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param KObjectConfig $config Configuration options.
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'actions'          => array('after.add'),
            'reference_column' => array('id', 'subtotal'),
        ));

        parent::_initialize($config);
    }

    /**
     * Command handler.
     *
     * @param KCommandInterface      $command The command.
     * @param KCommandChainInterface $chain   The chain executing the command.
     * @return mixed If a handler breaks, returns the break condition. Returns the result of the handler otherwise.
     */
    final public function execute(KCommandInterface $command, KCommandChainInterface $chain)
    {
        $action = $command->getName();

        if (in_array($action, $this->_actions))
        {
            $objects = $this->getEntityObject($command);

            foreach ($objects as $object)
            {
                if ($object instanceof KModelEntityInterface)
                {
                    $data = $this->getData($object);

                    $merchant = 'NUCLEON';
                    $password = 'eRGTsJ73DcjkL2J';

                    $parameters = array(
                        'merchantid'  => $merchant,
                        'txnid'       => $data['id'],
                        'amount'      => number_format($data['subtotal'], 2, '.', ''),
                        'ccy'         => 'PHP',
                        'description' => 'Order description.',
                        'email'       => $this->getObject('user')->getEmail(),
                    );

                    $parameters['key'] = $password;
                    $digest_string     = implode(':', $parameters);

                    unset($parameters['key']);

                    $parameters['digest'] = sha1($digest_string);

                    $url = 'http://test.dragonpay.ph/Pay.aspx?';
                    $url .= http_build_query($parameters, '', '&');

                    $this->getResponse()->setRedirect(JRoute::_($url, false));
                }
            }
        }
    }

    /**
     * Get relevant data out of an entity from the accounting system
     *
     * @param KModelEntityInterface $entity
     *
     * @return array
     */
    public function getData(KModelEntityInterface $entity)
    {
        $data = array();

        if (is_array($this->_reference_column))
        {
            foreach ($this->_reference_column as $column)
            {
                if ($entity->{$column}) {
                    $data[$column] = $entity->{$column};
                }
            }
        }
        elseif ($entity->{$this->_reference_column}) {
            die('test');
            $data[$this->_reference_column] = $entity->{$this->_reference_column};
        }

        return $data;
    }

    /**
     * Get the Entity object
     *
     * The Entity object is the entity on which the action is executed
     *
     * @param KCommandInterface $command
     *
     * @return KModelEntityInterface
     */
    public function getEntityObject(KCommandInterface $command)
    {
        $parts = explode('.', $command->getName());

        // Properly fetch data for the event.
        if ($parts[0] == 'before') {
            $object = $command->getSubject()->getModel()->fetch();
        } else {
            $object = $command->result;
        }

        return $object;
    }

    /**
     * Get the behavior name
     *
     * Hardcode the name
     *
     * @return string
     */
    final public function getName()
    {
        return 'onlinepayable';
    }

    /**
     * Get an object handle
     *
     * Force the object to be enqueued in the command chain
     *
     * @see execute()
     *
     * @return string A string that is unique, or NULL
     */
    final public function getHandle()
    {
        return KObjectMixinAbstract::getHandle();
    }
}
