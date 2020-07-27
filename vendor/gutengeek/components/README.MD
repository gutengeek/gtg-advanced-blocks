```# Gutengeek - Components Reference```

The package is a library that provides all components to create control power and UI beauty block inspector easily. This is a part of GutenGeek plugin. [Check out Full Features Gutengeek Blocks](https://wordpress.org/plugins/gtg-advanced-blocks).

## Installation

### Install the composer package

```bash
cd path/to/your-[plugin|theme]
composer require gutengeek/components
```

Make sure `autoload.php` had included
```php
// require autoload into your plugin or theme
require_once 'path/to/project/vendor/autoload.php';
```

### Or - Install the npm package
```bash
cd path/to/your-folder
npm i @gutengeek/components
```

### Requirement
```php
// make sure 'wp_enqueue_script' function enqueued with dependencies required
// example
wp_register_script( 'gutengeek-advanced-components', YOUR_PLUGIN_URL . 'path/to/blocks/scripts/index.js', [ 'wp-edit-post', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-compose', 'wp-data' ], false, true );
```

## Usage

Within [GutenGeek Plugin](https://gutengeek.com) plugin, these components can be accessed by importing from the `wp.gutengeek.components` root directory:

```jsx
// use with gtg-advanced-blocks wordpress plugin
const {
	BorderControl
} = wp.gutengeek.components

// import via npm
import Components from "@gutengeek/components";
import '@gutengeek/components/build/core.css';
import '@gutengeek/components/build/index.css';
```

```jsx
/**
 * GutenBerg dependencies
 */
const {
	InspectorControls
} = wp.blockEditor;

const { __ } = wp.i18n;

const {
	PanelBody,
} = wp.components;


/**
 * GutenGeek dependencies
 */
const {
	BorderControl
} = wp.gutengeek.components

export default function MyComponent() {
	const [ device, setDevice ] = useState( 'desktop' ); // desktop, tablet, mobile
	const [ value, setValue ] = useState( {} ); // border control value default is an object

	return <InspectorControls>
		<PanelBody title={ __('My Panel Body') }>
			<BorderControl
				className="your-extra-class"
				title={ __( 'Border' ) }
				value={ value }
				onChangeDevice={ ( device ) => setDevice(device) }
				device={ device }
				onChange={ value => setValue( value ) }
				units={ [ 'px', 'em', '%' ] }
			/>
		</PanelBody>
	</InspectorControls>
}
```

## Gutengeek Toolbar Components Available:

`ShapeControl`

`SizeTypeUnits`

`GradientControl`

`IconPickerControl`

`PaddingControl`

`MarginControl`

`HeadingControl`

`RangeSlider`

`BackgroundControl`

`Typography`

`AlignmentsControl`

`BorderControl`

`BorderRadiusControl`

`BoxShadowControl`

`TransformControl`

`ButtonGroupControl`

`Dimension`

`ParallaxControls`

`Color`

`MediaControl`

`TransitionControl`

`ResponsiveControl`

`InspectorTabs`

`InspectorTab`

------
## Development Guideline


### ShapeControl

By using Gutengeek, you can easily control all shape dividers which separate  the section of page. Able to choose shape top & shape bottom.

```jsx

const shapes = {
	'mask01' : '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
			 viewBox="0 0 1920 470" style="enable-background:new 0 0 1920 470;" xml:space="preserve">
		<path d="M0,0h1920v310L0,470V0z"/>
		</svg>'
	'mask02' : '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 1920 160" style="enable-background:new 0 0 1920 160;" xml:space="preserve">
	<g style="transform: rotate3d(-180, 0, 0, -180deg); transform-origin: center"><polygon points="1920,160 1920,0 0,160 "/></g>
	</svg>'
}
export default function MyComponent( props ) {
	const {
		attributes: {
			shape
		},
		setAttributes
	} = props
	const [ device, setDevice ] = useState( 'desktop' );

	return <InspectorControls>
		<PanelBody title={ __('My Panel Body') }>
			<ShapeControl value={ shape }
				shapes={shapes}
				device={ device }
			  	onChangeDevice={ ( device ) => setDevice( device ) }
			  	onChange={ ( value ) => setAttributes( { shape: value } ) }
		  	/>
		</PanelBody>
	</InspectorControls>
}
```

### SizeTypeUnits

Gutengeek offers options for units to measure size, type as px, em, rem, % in CSS.

```jsx
export default function MyComponent() {
	const [ value, setValue ] = useState( 'em' ); // default value maybe em, px, %, vh, vw, whatever you want

	return <InspectorControls>
		<PanelBody title={ __('My Panel Body') }>
			<SizeTypeUnit units={ ['px', 'em', '%'] } value={ value } onClick={ ( value ) => setValue( value ) }/>
		</PanelBody>
	</InspectorControls>
}
```

### GradientControl

Support CSS Gradients to let show off a smooth color between at least 2 or more colors.

```jsx
<GradientControl value={ value } onChange={ ( value ) => setAttributes({value: value}) } presetAllowed={true}/>
```

### IconPickerControl

Support to show/display all icons from font awesome to let you choose icon for button, list bullets, social network icon in order to impress customers at the first sight.

[![IconPickerControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/IconPicker.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)
```jsx
const icons = {
	500px: {
		label: "500px",
		search: {
			terms: []
		},
		styles: ["brands"],
		svg: {
			brands: {
				path: "M103.3 344.3c-6.5-14.2-6.9-18.3 7.4-23.1 25.6-8 8 9.2 43.2 49.2h.3v-93.9c1.2-50.2 44-92.2 97.7-92.2 53.9 0 97.7 43.5 97.7 96.8 0 63.4-60.8 113.2-128.5 93.3-10.5-4.2-2.1-31.7 8.5-28.6 53 0 89.4-10.1 89.4-64.4 0-61-77.1-89.6-116.9-44.6-23.5 26.4-17.6 42.1-17.6 157.6 50.7 31 118.3 22 160.4-20.1 24.8-24.8 38.5-58 38.5-93 0-35.2-13.8-68.2-38.8-93.3-24.8-24.8-57.8-38.5-93.3-38.5s-68.8 13.8-93.5 38.5c-.3.3-16 16.5-21.2 23.9l-.5.6c-3.3 4.7-6.3 9.1-20.1 6.1-6.9-1.7-14.3-5.8-14.3-11.8V20c0-5 3.9-10.5 10.5-10.5h241.3c8.3 0 8.3 11.6 8.3 15.1 0 3.9 0 15.1-8.3 15.1H130.3v132.9h.3c104.2-109.8 282.8-36 282.8 108.9 0 178.1-244.8 220.3-310.1 62.8zm63.3-260.8c-.5 4.2 4.6 24.5 14.6 20.6C306 56.6 384 144.5 390.6 144.5c4.8 0 22.8-15.3 14.3-22.8-93.2-89-234.5-57-238.3-38.2zM393 414.7C283 524.6 94 475.5 61 310.5c0-12.2-30.4-7.4-28.9 3.3 24 173.4 246 256.9 381.6 121.3 6.9-7.8-12.6-28.4-20.7-20.4zM213.6 306.6c0 4 4.3 7.3 5.5 8.5 3 3 6.1 4.4 8.5 4.4 3.8 0 2.6.2 22.3-19.5 19.6 19.3 19.1 19.5 22.3 19.5 5.4 0 18.5-10.4 10.7-18.2L265.6 284l18.2-18.2c6.3-6.8-10.1-21.8-16.2-15.7L249.7 268c-18.6-18.8-18.4-19.5-21.5-19.5-5 0-18 11.7-12.4 17.3L234 284c-18.1 17.9-20.4 19.2-20.4 22.6z"
			},
			viewBox: [0, 0, 448, 512]
		}
	},
	search: {
		label: "Ad",
		search: {
			terms: []
		},
		styles: ["solid"],
		svg: {
			solid: {
				path: ""M157.52 272h36.96L176 218.78 157.52 272zM352 256c-13.23 0-24 10.77-24 24s10.77 24 24 24 24-10.77 24-24-10.77-24-24-24zM464 64H48C21.5 64 0 85.5 0 112v288c0 26.5 21.5 48 48 48h416c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48zM250.58 352h-16.94c-6.81 0-12.88-4.32-15.12-10.75L211.15 320h-70.29l-7.38 21.25A16 16 0 0 1 118.36 352h-16.94c-11.01 0-18.73-10.85-15.12-21.25L140 176.12A23.995 23.995 0 0 1 162.67 160h26.66A23.99 23.99 0 0 1 212 176.13l53.69 154.62c3.61 10.4-4.11 21.25-15.11 21.25zM424 336c0 8.84-7.16 16-16 16h-16c-4.85 0-9.04-2.27-11.98-5.68-8.62 3.66-18.09 5.68-28.02 5.68-39.7 0-72-32.3-72-72s32.3-72 72-72c8.46 0 16.46 1.73 24 4.42V176c0-8.84 7.16-16 16-16h16c8.84 0 16 7.16 16 16v160z""
			},
			viewBox: [0, 0, 512, 512]
		}
	}
}
<IconPickerControl icons={icons} renderIcon={(icon) => {
		console.log(icon);
		return (
			// render your icon here
		)
	} }
	label={ __( 'Icon' ) }
	value={ value }
	onChange={ value => setAttributes( { value: value } ) }
/>
// renderIcon is optional, default icon will be render as svg
```

### PaddingControl

Set space for around element's contents,. There are settings for padding ( top, right, bottom, left)

[![PaddingControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/Padding.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)
```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<PaddingControl
	value={ padding }
	label={__( 'Padding' )}
	onChange={ ( value ) => setAttributes({ padding: value } )}
	device={ device}
	onChangeDevice={ (value) => setDevice(value) } units={ [ 'px', 'em', '%' ] }
/>
```

### MarginControl

Setting the margin for each side of an element with border (top, right, bottom, and left).

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<MarginControl
	value={ margin }
	label={__( 'Margin' )}
	onChange={ ( value ) => setAttributes({ margin: value } )}
	device={ device}
	onChangeDevice={ (value) => setDevice(value) } units={ [ 'px', 'em', '%' ] }
/>
```

### HeadingControl

Allows to choose heading tag: h1, h2, h3, h4, h5, h6

```jsx
<HeadingControl
	label={ __( 'Heading Tag' ) }
	value={ headingTag }
	onChange={ ( value ) => setAttributes( { headingTag: value } ) }
/>
```

### RangeSlider

Drag and drop number, support on the different devices.

[![RangeSlider](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/RangeSlider.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<RangeSlider
	label={ __( 'Column Gap' ) }
	value={ marginBottom } min={ 1 }
	max={ 33 }
	onChange={ ( value ) => setAttributes( { marginBottom: value } ) }
	units={['px', 'em', '%']}
	min={0}
	max={100}
	device={device}
	onChangeDevice={ ( device ) => setDevice( device ) }
	allowReset
/>
```

### BackgroundControl

Choose Normal & Hover. Possible to set background: Color, Gradient, Video &  Images

[![BackgroundControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/Background.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<BackgroundControl
	className="your-extra-class"
	label={ __( 'Background' ) }
	value={ background }
	allows={ [ 'color', 'gradient', 'image' ] }
	device={ gutengeek_device_mode }
	onChange={ value => setAttributes( { background: value } ) }
	onChangeDevice={ ( device ) => setDevice( device ) }
/>
```

### Typography

Font size, font family, variants, font style, font weight, text transform controls

[![Typography](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/Typography.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<Typography
	label={ __( 'Typography' ) }
	value={ headingTypo }
	onChange={ value => setAttributes( { headingTypo: value } ) }
	device={ gutengeek_device_mode }
	onChangeDevice={ value => setDevice(value) }
/>
```

### AlignmentsControl

Possible to set text-align in CSS with Align center, wide width, full width

[![AlignmentsControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/HeadingControl.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
<AlignmentsControl
	className="your-prefix"
	label={ __( 'Text Alignment' ) }
	device={ device }
	onChangeDevice={ ( device ) => setDevice( device ) }
	value={ value }
	onChange={ ( value ) => setAttributes( { value: value } ) }
	toggle={true}
	options={[
		{ label: __('Left'), value: 'left', text: __( 'Left' ) },
		{ label: __('Center'), value: 'center', text: __( 'Center' ) },
		{ label: text: __( 'Right' ), value: 'right', text: __( 'Right' ) },
	]}
/>
```

### BorderControl

Enable/disable border in css, set position, width (px, em, %), border shape.

[![BorderControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/BorderControl.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
<BorderControl
	className="your-extra-class"
	title={ __( 'Border' ) }
	value={ value }
	onChangeDevice={ ( device ) => setDevice(device) }
	device={ device }
	onChange={ value => setValue( value ) }
	units={ [ 'px', 'em', '%' ] }
/>
```

### BorderRadius

Defines the radius in CSS of the element's corners

[![BorderRadius](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/BorderRadius.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
<BorderRadiusControl
	className="your-extra-classs"
	label={ __( 'Border Radius' ) }
	units={ [ 'px', 'em', '%' ] }
	value={ value }
	responsive={ true }
	device={ device }
	onChangeDevice={ ( device ) => setDevice( device ) }
	min={ 0 }
	max={ 100 }
	onChange={ value => setAttributes( { value: value } ) }
/>
```

### BoxShadowControl

Choose box shadow in CSS

[![BoxShadowControl](https://s3.us-east-2.amazonaws.com/static.gutengeek.com/Thumbnails/BoxShadow.png)](https://wordpress.org/plugins/gtg-advanced-blocks/)

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```

```jsx
<BoxShadowControl
	value={ boxShadow } device={ device }
	unit='px'
	onChangeDevice={ ( device ) => setDevice( device ) }
	onChange={ value => setAttributes( { boxShadow: value } ) }
/>
```

### TransformControl

All attribute transform in CSS

```jsx
const [ device, setDevice ] = useState( 'desktop' );
```
```jsx
<TransformControl
	label={ __( 'Transform' ) }
	value={ transform }
	onChange={(value) => setAttributes( { transform: value } )}
	device={ device} onChangeDevice={ ( device ) => setDevice( device ) }
/>
```

### ButtonGroupControl

Enable/disable buttons, type, size, enable/disable typography, Text, link (Open new window, same window), margin & icon. Only hover ( enable/disable), Icon Position, Icon Spacing,

```jsx
<ButtonGroupControl
	className="your-extra-class"
	label={ __( 'Button Position' ) }
	value={ position }
	options={ [
		{ value: 'left', label: <Dashicon icon="arrow-left"/>, text: __( 'Left' ) },
		{ value: 'right', label: <Dashicon icon="arrow-right"/>, text: __( 'Right' ) },
	] }
	toggle={ true }
	onClick={ ( value ) => setAttributes( { position: value } ) }
/>
```

### Dimension

Set padding & margin with Top, Right, Bottom & Left with options px, em, rem, % in CSS.

```jsx
<Dimension
	label={ __( 'Card Padding' ) }
	value={ cardPadding }
	device={ gutengeek_device_mode }
	min={ 0 }
	max={ 500 }
	onChangeDevice={ ( device ) => this.props.setDevice(device) }
	onChange={ value => setAttributes( { panelSpacing: value } ) }
/>
```


### ParallaxControls

Enable/disable parallax control, event type, items #1 (select image, width, height, position, Speed, Duration, Index)

```jsx
<ParallaxControls parallax={parallax} onChange={(value) => setAttributes({ parallax: { ...parallax, ...value } })}/>
```

### Color

Pick a suitable color from pallet

```jsx
<Color className="your-extra-class" label={ __( 'Color' ) } value={ color } onChange={ ( value ) => setAttributes( { color: value } ) }/>
```

### Media

Upload media to library or import media url.

```jsx
<MediaControl
	label={ __( 'Select Image' ) } multiple={ false }
	type={ [ 'image', 'color', 'gradient', 'video' ] }
	value={ iconImg } panel={ true }
	onChange={ media => setAttributes( { iconImg: media } ) }
/>
```

### TransitionControl

Attributes transition effects controls in CSS

```jsx
<TransitionControl value={ transition } onChange={(value) => setAttributes({ transition: value } )}/>
```

### ResponsiveControl

Options for Hide/enable on devices as desktop, tablet, mobile

```jsx
const [ device, setDevice ] = useState( 'desktop' );
const [ unit, setUnit ] = useState( 'px' );
```

```jsx
<ResponsiveControl label={ label }
		device={ device }
		units={ [ 'px', 'em' ] }
		unit={ unit }
		onChangeSizeType={ ( value ) => setUnit( value ) }
		onChangeDevice={ ( value ) => setDevice( value ) }>
{
	( deviceMode ) => {
		return (
			// return your children component
		);
	}
}
```


### InspectorTabs & InspectorTab

Split tab layout, style, advanced

```jsx
<InspectorControls>
	<InspectorTabs tabs={[ 'layout', 'style', 'advanced' ]} active="advanced">
		<InspectorTab key="layout">
			// do something
		</InspectorTab>
		<InspectorTab key="style">
			// do something
		</InspectorTab>
		<InspectorTab key="advanced">
			// do something
		</InspectorTab>
	</InspectorTabs>
</InspectorControls>
```

### Tabs & Tab

Split tab hover, active state

```jsx
<Tabs tabs={[ { name: 'normal', label: __( 'Normal' ) }, { name: 'hover', label: __( 'Hover' ) } ]} onChange={(value) => setState( { state: value } )}>
	<Tab key="normal">
		// do something
	</Tab>
	<Tab key="hover">
		// do something
	</Tab>
</Tabs>
```


### URL

URL full option

```jsx
// imageUrl: object
<URL
	label={ __( 'URL' ) }
	value={imageUrl}
	onChange={value => setAttributes({ imageUrl: value })}
/>
```
