
var el = wp.element.createElement;

var InspectorControls;
if (typeof window.wp.blockEditor !== 'undefined'){
	InspectorControls = window.wp.blockEditor.InspectorControls;
} else {
	InspectorControls = wp.editor.InspectorControls;
}

const gpp_fp_layouts_icon = wp.element.createElement('svg', 
	{ 
		width: 24, 
		height: 24 
	},
		el('path', {d: "M0,20.6h10.6v-1.3H0V20.6z M0,23.9h10.6v-1.3H0V23.9z M1.6,11.4h7.2c0.9,0,1.6-0.7,1.6-1.6V1.6 C10.4,0.7,9.7,0,8.8,0H1.6C0.7,0,0,0.7,0,1.6v8.3C0,10.7,0.7,11.4,1.6,11.4z M1.3,1.6c0-0.1,0.1-0.3,0.3-0.3h7.2 c0.1,0,0.3,0.1,0.3,0.3v8.3c0,0.1-0.1,0.3-0.3,0.3H1.6c-0.1,0-0.3-0.1-0.3-0.3V1.6z M0,17.4h10.6v-1.3H0V17.4z M12.7,5.1h4.1 c0.4,0,0.7-0.3,0.7-0.7V0.7c0-0.4-0.3-0.7-0.7-0.7h-4.1c-0.4,0-0.7,0.3-0.7,0.7v3.7C11.9,4.8,12.3,5.1,12.7,5.1z M13.2,1.3h2.9v2.5 h-2.9V1.3z M12.7,11.4h4.1c0.4,0,0.7-0.3,0.7-0.7V7c0-0.4-0.3-0.7-0.7-0.7h-4.1c-0.4,0-0.7,0.3-0.7,0.7v3.7 C11.9,11.1,12.3,11.4,12.7,11.4z M13.2,7.6h2.9v2.5h-2.9V7.6z M19.2,10.6H24V9.3h-4.8V10.6z M19.2,4.4H24V3.1h-4.8V4.4z M13.4,23.9 H24v-1.3H13.4V23.9z M19.2,0.8v1.3H24V0.8H19.2z M19.2,8.4H24V7.1h-4.8V8.4z M13.4,20.6H24v-1.3H13.4V20.6z M0,14.2h24v-1.3H0V14.2z M13.4,17.4H24v-1.3H13.4V17.4z"})
);

wp.blocks.registerBlockType('gpp/fp-layouts', {
	title: 'GPP Frontpage Layouts',
	description: 'Layouts for the frontpage.',
	icon: gpp_fp_layouts_icon,
	category: 'design',
	attributes: {
		title: {
			type: 'string',
			default: ''
		},
		category: {
			type: 'array',
			default: ''
		},
		layout: {
			type: 'number',
			default: 1
		},
		header_color: {
			type: 'string',
			default: '#0670B7'
		},
		offset: {
			type: 'number',
			default: 0
		},
		white_text: {
			type: 'boolean',
			default: false
		}
	},
	edit: function(props){
		var attr = props.attributes;
		var layouts = [];
		for(let i = 1; i <= gpp_fp_layouts.layouts; i++){
			layouts.push({value: i, label: 'Layout' + ' ' + i});
		}
		return [
			wp.element.createElement(
				wp.components.ServerSideRender, {
					block: 'gpp/fp-layouts',
					attributes: props.attributes
				}
			),
			wp.element.createElement(
				wp.editor.InspectorControls,
				{key: 'inspector'},
				wp.element.createElement(
					wp.components.PanelBody,
					{
						title: 'Settings',
						initialOpen: true
					}, wp.element.createElement(
						wp.components.TextControl,
						{
							type: 'text',
							label: 'Title',
							value: attr.title,
							onChange: function(val){
								props.setAttributes({title: val})
							}
						}
					), wp.element.createElement(
						wp.components.SelectControl,
						{
							label: 'Layout',
							options: layouts,
							value: attr.layout,
							onChange: function(val){
								props.setAttributes({layout: parseInt(val)})
							}
						}
					), wp.element.createElement(
						wp.components.SelectControl,
						{
							label: 'Category',
							value: attr.category,
							options: gpp_fp_layouts.categories,
							multiple: true,
							onChange: function(val){
								props.setAttributes({category: val})
							}
						}
					), wp.element.createElement(
						wp.components.RangeControl,
						{
							label: 'Offset',
							min: 0,
							value: attr.offset,
							onChange: function(val){
								props.setAttributes({offset: val})
							}
						}
					), wp.element.createElement(
						wp.components.BaseControl,
						{
							label: 'Header color'
						}, wp.element.createElement(
						wp.components.ColorPicker,
						{
							color: attr.header_color,
							onChangeComplete: function(val){
								props.setAttributes({header_color: val.hex})
							}
						}
					)), wp.element.createElement(
						wp.components.CheckboxControl,
						{
							label: 'Show white text',
							checked: attr.white_text,
							onChange: function(val){
								props.setAttributes({white_text: val})
							}
						}
					)
				)
			)
		];
	},
	save: function(props){
		//Render in php
		return null;
	}
});