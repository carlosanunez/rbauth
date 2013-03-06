<?php namespace EdvinasKrucas\RBAuth;

use Illuminate\Auth\Guard;
use EdvinasKrucas\RBAuth\Contracts\RoleProviderInterface;

class RBAuth extends Guard
{
    /**
     * Role provider implementation.
     *
     * @var Contracts\RoleProviderInterface
     */
    protected $roleProvider;

    /**
     * Create new RBAuth instance.
     *
     * @param UserProviderInterface $provider
     * @param SessionStore $session
     * @param Contracts\RoleProviderInterface $roleProvider
     */
    public function __construct(UserProviderInterface $provider,
                                SessionStore $session,
                                RoleProviderInterface $roleProvider)
    {
        parent::__construct($provider, $session);
        $this->roleProvider = $roleProvider;
    }

    /**
     * Determines if a user has certain permission.
     * If user is not logged then checks for role permission.
     *
     * @param $identifier
     * @return bool
     */
    public function can($identifier)
    {
        if(!is_null($this->user()))
        {
            return $this->user()->can($identifier);
        }
        else
        {
            $role = $this->roleProvider->getByName($this->app['config']->get('rbauth::default_role'));

            if($role)
            {
                return $role->can($identifier);
            }
        }

        return false;
    }

    /**
     * Determines if a user has a given role.
     *
     * @param $roleName
     * @return bool
     */
    public function is($roleName)
    {
        if(!is_null($this->user()))
        {
            foreach($this->user()->getRoles() as $role)
            {
                if($role->getRoleName() == $roleName)
                {
                    return true;
                }
            }
        }

        return false;
    }
}