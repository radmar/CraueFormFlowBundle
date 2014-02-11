<?php

namespace Craue\FormFlowBundle\Tests\Form;

use Craue\FormFlowBundle\Event\GetStepsEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @group unit
 *
 * @author Toni Uebernickel <tuebernickel@gmail.com>
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2014 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormFlowTest extends \PHPUnit_Framework_TestCase {

	public function testStepListener() {
		$steps = array(
			$this->getMock('\Craue\FormFlowBundle\Form\StepInterface'),
		);

		$dispatcher = new EventDispatcher();
		$dispatcher->addListener(FormFlowEvents::GET_STEPS, function(GetStepsEvent $event) use ($steps) {
			$event->setSteps($steps);

			$event->stopPropagation();
		});

		/* @var $flow \PHPUnit_Framework_MockObject_MockObject|\Craue\FormFlowBundle\Form\FormFlow */
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');
		$flow->setEventDispatcher($dispatcher);

		$this->assertEquals($steps, $flow->getSteps());
	}

	public function testCreateStepsFromConfig_fixArrayIndexes() {
		$flowStub = $this->getMock('\Craue\FormFlowBundle\Form\FormFlow', array('getName', 'loadStepsConfig'));

		$flowStub
			->expects($this->once())
			->method('loadStepsConfig')
			->will($this->returnValue(array(
				2 => array(
					'label' => 'step1',
				),
			)))
		;

		$this->assertSame(1, $flowStub->getStep(1)->getNumber());
	}

	/**
	 * Ensure that the "validation_groups" option can be overridden.
	 */
	public function testGetFormOptions_overrideValidationGroups() {
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');

		$options = $flow->getFormOptions(1, array(
			'validation_groups' => 'Default',
		));

		$this->assertEquals('Default', $options['validation_groups']);
	}

	/**
	 * Ensure that the generated default value for "validation_groups" is an array, which can be used to just add
	 * other groups.
	 */
	public function testGetFormOptions_generatedValidationGroupIsArray() {
		$flow = $this->getMockForAbstractClass('\Craue\FormFlowBundle\Form\FormFlow');

		$flow
			->expects($this->once())
			->method('getName')
			->will($this->returnValue('createTopic'))
		;

		$options = $flow->getFormOptions(1);

		$this->assertEquals(array('flow_createTopic_step1'), $options['validation_groups']);
	}

}
