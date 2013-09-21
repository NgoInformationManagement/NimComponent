<?php

/*
 * This file is part of the NIM package.
 *
 * (c) Langlade Arnaud
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\NIM\Component\Twig;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Intl\Intl;

class NimCountryExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('NIM\Component\Twig\NimCountryExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_should_return_country_name($countryBundle, $intl)
    {
        // TODO : find the right way to mock it
        Intl::getRegionBundle()->getCountryName(Argument::type('string'))
            ->willReturn('England');

        $this->countryFilter('en', 'en')->shouldReturn('England');
    }

    function it_should_return_country_code()
    {
        $this->countryFilter('en', 'en')->shouldReturn('en');
    }
}
