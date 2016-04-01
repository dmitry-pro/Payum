<?php
namespace Payum\Paypal\ExpressCheckout\Nvp\Tests;

use Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutPermissionGatewayFactory;

class PaypalExpressCheckoutPermissionGatewayFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldSubClassGatewayFactory()
    {
        $rc = new \ReflectionClass('Payum\Paypal\ExpressCheckout\Nvp\PaypalExpressCheckoutPermissionGatewayFactory');

        $this->assertTrue($rc->isSubclassOf('Payum\Core\GatewayFactory'));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        new PaypalExpressCheckoutPermissionGatewayFactory();
    }

    /**
     * @test
     */
    public function shouldCreateCoreGatewayFactoryIfNotPassed()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $this->assertAttributeInstanceOf('Payum\Core\CoreGatewayFactory', 'coreGatewayFactory', $factory);
    }

    /**
     * @test
     */
    public function shouldUseCoreGatewayFactoryPassedAsSecondArgument()
    {
        $coreGatewayFactory = $this->getMock('Payum\Core\GatewayFactoryInterface');

        $factory = new PaypalExpressCheckoutPermissionGatewayFactory(array(), $coreGatewayFactory);

        $this->assertAttributeSame($coreGatewayFactory, 'coreGatewayFactory', $factory);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGateway()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $gateway = $factory->create(array(
            'username' => 'aName',
            'password' => 'aPass',
            'signature' => 'aSign',
            'token' => 'aToken',
            'tokenSecret' => 'aTokenSecret',
            'third_party_subject' => 'a_third_party_subject',
        ));

        $this->assertInstanceOf('Payum\Core\Gateway', $gateway);

        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);

        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGatewayWithCustomApi()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $gateway = $factory->create(array('payum.api' => new \stdClass()));

        $this->assertInstanceOf('Payum\Core\Gateway', $gateway);

        $this->assertAttributeNotEmpty('apis', $gateway);
        $this->assertAttributeNotEmpty('actions', $gateway);

        $extensions = $this->readAttribute($gateway, 'extensions');
        $this->assertAttributeNotEmpty('extensions', $extensions);
    }

    /**
     * @test
     */
    public function shouldAllowCreateGatewayConfig()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $config = $factory->createConfig();

        $this->assertInternalType('array', $config);
        $this->assertNotEmpty($config);
    }

    /**
     * @test
     */
    public function shouldAddDefaultConfigPassedInConstructorWhileCreatingGatewayConfig()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory(array(
            'foo' => 'fooVal',
            'bar' => 'barVal',
        ));

        $config = $factory->createConfig();

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('foo', $config);
        $this->assertEquals('fooVal', $config['foo']);

        $this->assertArrayHasKey('bar', $config);
        $this->assertEquals('barVal', $config['bar']);
    }

    /**
     * @test
     */
    public function shouldConfigContainDefaultOptions()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $config = $factory->createConfig();

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('payum.default_options', $config);
        $this->assertEquals(
            array('username' => '', 'password' => '', 'signature' => '', 'token' => '', 'tokenSecret' => '', 'third_party_subject' => '', 'sandbox' => true),
            $config['payum.default_options']
        );
    }

    /**
     * @test
     */
    public function shouldConfigContainFactoryNameAndTitle()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $config = $factory->createConfig();

        $this->assertInternalType('array', $config);

        $this->assertArrayHasKey('payum.factory_name', $config);
        $this->assertEquals('paypal_express_checkout_nvp', $config['payum.factory_name']);

        $this->assertArrayHasKey('payum.factory_title', $config);
        $this->assertEquals('PayPal ExpressCheckout', $config['payum.factory_title']);
    }

    /**
     * @test
     *
     * @expectedException \Payum\Core\Exception\LogicException
     * @expectedExceptionMessage The username, password, signature, token, tokenSecret, third_party_subject fields are required.
     */
    public function shouldThrowIfRequiredOptionsNotPassed()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $factory->create();
    }

    /**
     * @test
     */
    public function shouldConfigurePaths()
    {
        $factory = new PaypalExpressCheckoutPermissionGatewayFactory();

        $config = $factory->createConfig();

        $this->assertInternalType('array', $config);
        $this->assertNotEmpty($config);

        $this->assertInternalType('array', $config['payum.paths']);
        $this->assertNotEmpty($config['payum.paths']);

        $this->assertArrayHasKey('PayumCore', $config['payum.paths']);
        $this->assertStringEndsWith('Resources/views', $config['payum.paths']['PayumCore']);
        $this->assertTrue(file_exists($config['payum.paths']['PayumCore']));

        $this->assertArrayHasKey('PayumPaypalExpressCheckout', $config['payum.paths']);
        $this->assertStringEndsWith('Resources/views', $config['payum.paths']['PayumPaypalExpressCheckout']);
        $this->assertTrue(file_exists($config['payum.paths']['PayumPaypalExpressCheckout']));
    }
}
