<?php

namespace IDCI\Bundle\BarcodeBundle\Generator;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

use IDCI\Bundle\BarcodeBundle\Type\Type;
use IDCI\Bundle\BarcodeBundle\Lib\DNS2D;
use IDCI\Bundle\BarcodeBundle\Lib\DNS1D;

/**
 * Class Generator
 */
class Generator
{
    /**
     * @var DNS2D
     */
    protected $dns2d;

    /**
     * @var DNS1D
     */
    protected $dns1d;

    /**
     * @var OptionsResolver
     */
    protected $resolver;

    /**
     * @var array
     */
    protected $formatFunctionMap = array(
        'svg'  => 'getBarcodeSVG',
        'html' => 'getBarcodeHTML',
        'png'  => 'getBarcodePNG',
    );

    /**
     * construct
     */
    public function __construct()
    {
        $this->dns2d = new DNS2D();
        $this->dns1d = new DNS1D();
        $this->resolver = new OptionsResolver();
        $this->configureOptions($this->resolver);
    }

    /**
     * @param array $options
     *        string $code   code to print
     *        string $type   type of barcode
     *        string $format output format
     *        int    $width  Minimum width of a single bar in user units.
     *        int    $height Height of barcode in user units.
     *        string $color  Foreground color (in SVG format) for bar elements (background is transparent).
     *
     * @return mixed
     */
    public function generate($options = array())
    {
        $options = $this->resolver->resolve($options);

        if (Type::getDimension($options['type']) == '2D') {
            return call_user_func_array(
                array(
                    $this->dns2d,
                    $this->formatFunctionMap[$options['format']],
                ),
                array($options['code'], $options['type'], $options['width'], $options['height'], $options['color'])
            );
        } else {
            return call_user_func_array(
                array(
                    $this->dns1d,
                    $this->formatFunctionMap[$options['format']],
                ),
                array($options['code'], $options['type'], $options['width'], $options['height'], $options['color'])
            );
        }
    }

    /**
     * Configure generate options
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array(
                'code', 'type', 'format',
            ))
            ->setDefaults(array(
                'width' => function (Options $options) {
                    return Type::getDimension($options['type']) == '2D' ? 5 : 2;
                },
                'height' => function (Options $options) {
                    return Type::getDimension($options['type']) == '2D' ? 5 : 30;
                },
                'color' => function (Options $options) {
                    return $options['format'] == 'png' ? array(0, 0, 0) : 'black';
                },
            ))
            ->setAllowedTypes('code', array('string'))
            ->setAllowedTypes('type', array('string'))
            ->setAllowedTypes('format', array('string'))
            ->setAllowedTypes('width', array('integer'))
            ->setAllowedTypes('height', array('integer'))
            ->setAllowedTypes('color', array('string', 'array'))
            ->setAllowedValues('type', array_merge(Type::$oneDimensionalBarcodeType, Type::$twoDimensionalBarcodeType))
            ->setAllowedValues('format', array('html', 'png', 'svg'))
        ;
    }
}
