<?php
/**
 * CMB2_Field tests.
 *
 * @author    CMB2 team
 * @license   GPL-2.0+
 *
 * @link      https://cmb2.io
 */
require_once 'cmb-tests-base.php';

/**
 * Test the oEmbed functionality.
 */
class Test_CMB2_Ajax extends Test_CMB2
{
    /**
     * Set up the test fixture.
     */
    public function setUp()
    {
        parent::setUp();

        $this->cmb = cmb2_get_metabox([
            'id'      => 'metabox_id',
            'hookup'  => false,
            'show_on' => [
                'key'   => 'options-page',
                'value' => 'options-page-id',
            ],
            'fields' => [
                [
                    'id'   => 'test_embed',
                    'type' => 'oembed',
                ],
                [
                    'id'   => 'another_value',
                    'type' => 'text',
                ],
            ],
        ], 'options-page-id', 'options-page');

        $this->oembed_args = [
            'url'         => 'https://www.youtube.com/watch?v=NCXyEKqmWdA',
            'object_id'   => 'options-page-id',
            'object_type' => 'options-page',
            'oembed_args' => [
                'width' => '640',
            ],
            'field_id'    => 'test_embed',
            'src'         => 'https://www.youtube.com/embed/NCXyEKqmWdA?feature=oembed',
        ];
    }

    public function tearDown()
    {
        delete_option($this->oembed_args['object_id']);
        parent::tearDown();
    }

    public function test_cmb2_ajax_instance()
    {
        $this->assertInstanceOf('CMB2_Ajax', cmb2_ajax());
    }

    public function test_correct_properties()
    {
        $this->assertEquals($this->oembed_args['object_id'], $this->cmb->object_id());
        $this->assertEquals($this->oembed_args['object_type'], $this->cmb->object_type());
    }

    /**
     * @group cmb2-ajax-embed
     */
    public function test_get_oembed()
    {
        $args = $this->oembed_args;

        $args['oembed_result'] = [
            sprintf('<iframe width="640" height="360" src="%s"', $args['src']),
            '></iframe>',
        ];
        $this->assertOEmbedResult($args);

        // Test another oembed URL
        $args['url'] = 'https://twitter.com/Jtsternberg/status/703434891518726144';

        $args['oembed_result'] = [
            '<blockquote class="twitter-tweet" ',
            '<p lang="en" dir="ltr">That time we did Adele’s “Hello” at ',
            '<a href="https://t.co/aq89T5VM5x">https://t.co/aq89T5VM5x</a></p>&mdash; Justin Sternberg (@Jtsternberg) ',
            sprintf('<a href="%1$s', $args['url']),
            '">February 27, 2016</a></blockquote><script async src="',
            'platform.twitter.com/widgets.js',
            '</script>',
        ];

        $this->assertOEmbedResult($args);
    }

    /**
     * @group cmb2-ajax-embed
     */
    public function test_values_cached()
    {
        $options = $this->get_option();

        $expected = [
            '_oembed_887df34cb3e109936f1e848042f873a3' => [
                '<iframe',
                'src="https://www.youtube.com/embed/NCXyEKqmWdA?feature=oembed"',
                '</iframe>',
            ],
            '_oembed_bc2b74b277d0e39ae9ec91eefaee8e31' => ['{{unknown}}'],
        ];

        if ($this->is_3_8() && $this->is_connected()) {
            foreach ($expected as $key => $value) {
                $this->assertVerifiersMatch(['connected' => $value], $options[$key]);
            }
        } else {
            $opt_keys = array_keys($options);
            $opt_values = array_values($options);

            $_expected = $this->is_connected() ? [
                $expected['_oembed_887df34cb3e109936f1e848042f873a3'],
                'time_1',
                [
                    '<blockquote class="twitter-tweet"',
                    'That time we did Adele’s “Hello” at <a href="https://twitter.com/generationschch',
                    '>@generationschch</a>…<a href="https://t.co/aq89T5VM5x">https://t.co/aq89T5VM5x</a></p>&mdash; Justin Sternberg (@Jtsternberg) <a href="https://twitter.com/Jtsternberg/status/703434891518726144',
                    'February 27, 2016</a></blockquote><script async src="',
                    'platform.twitter.com/widgets.js" charset="utf-8"></script>',
                ],
                'time_2',
            ] : [
                $expected['_oembed_bc2b74b277d0e39ae9ec91eefaee8e31'][0],
                $expected['_oembed_bc2b74b277d0e39ae9ec91eefaee8e31'][0],
            ];

            foreach ($_expected as $key => $expected_value) {
                $opt_key = $opt_keys[$key];
                $val = $opt_values[$key];

                if (is_array($expected_value)) {
                    $this->assertVerifiersMatch(['connected' => $expected_value], $val);
                } else {
                    if (0 !== strpos($expected_value, 'time_')) {
                        $this->assertHTMLstringsAreEqual($expected_value, $opt_values[$key]);
                        $this->assertTrue(0 === strpos($opt_key, '_oembed_'));
                    } else {
                        $this->assertTrue(0 === strpos($opt_key, '_oembed_time_'));
                        $this->assertTrue(is_int($opt_values[$key]));
                    }
                }
            }
        }// End if().
    }

    public function test_get_oembed_delete_with_expired_ttl()
    {
        add_filter('oembed_ttl', '__return_zero');
        add_action('cmb2_save_options-page_fields', ['CMB2_Ajax', 'clean_stale_options_page_oembeds']);

        $new = [
            'another_value' => 'value',
        ];
        if ($this->is_3_8()) {
            $new = [
                '_oembed_887df34cb3e109936f1e848042f873a3' => '<iframe width="640" height="360" src="https://www.youtube.com/embed/NCXyEKqmWdA?feature=oembed" frameborder="0" allowfullscreen></iframe>',
            ];
        }
        $_POST = array_merge($new, $this->get_option());

        $this->cmb->save_fields();
        $options = $this->get_option();

        if ($this->is_3_8()) {
            foreach ([
                '<iframe',
                'src="https://www.youtube.com/embed/NCXyEKqmWdA?feature=oembed"',
                '</iframe>',
            ] as $part) {
                $this->assertTrue(false !== strpos($options['_oembed_887df34cb3e109936f1e848042f873a3'], $part), $part);
            }
        } else {
            $this->assertEquals($new, $options);
        }
    }

    protected function get_option()
    {
        return cmb2_options($this->oembed_args['object_id'])->get_options();
    }

    protected function is_3_8()
    {
        return !CMB2_Utils::wp_at_least('3.8.1');
    }
}
