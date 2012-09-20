<?php
namespace Armd\SocialAuthBundle\Security\Authentication\Provider;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class AuthenticationProviderParametersReader
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getParameters($authProviderName)
    {
        $host = $this->container->get('request')->getHost();
        $firewallParams = $this->container->getParameter('armd_social_auth.firewall_parameters');

        if(!empty($firewallParams['auth_provider_parameters'][$host][$authProviderName])) {
            $providerParams = $firewallParams['auth_provider_parameters'][$host][$authProviderName];
        } elseif(!empty($firewallParams['auth_provider_parameters']['default'][$authProviderName])) {
            $providerParams = $firewallParams['auth_provider_parameters']['default'][$authProviderName];
        } else {
            throw new InvalidConfigurationException('Can\'t find authentication parameters for provider ' . $authProviderName
            . ' and host ' . $host . '. Check firewall armd_social_auth.auth_provider_parameters config.');
        }

        return $providerParams;
    }
}