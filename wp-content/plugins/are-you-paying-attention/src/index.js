const { useBlockProps } = wp.blockEditor;
wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  apiVersion: 3,
  icon: "smiley",
  category: "common",
  attributes: {
    skyColor: { type: "string" },
    grassColor: { type: "string" },
  },
  edit: (props) => {
    function updateSkyColor(event) {
      props.setAttributes({ skyColor: event.target.value });
    }
    function updateGrassColor(event) {
      props.setAttributes({ grassColor: event.target.value });
    }
    const blockProps = useBlockProps();
    return (
      <div {...blockProps}>
        <input
          type="text"
          placeholder="sky Color"
          value={props.attributes.skyColor}
          onChange={updateSkyColor}
        />
        <input
          type="text"
          placeholder="grass Color"
          value={props.attributes.grassColor}
          onChange={updateGrassColor}
        />
      </div>
    );
  },
  save: (props) => {
    const blockProps = useBlockProps.save();
    // Dejamos el save simple sin blockProps, que es totalmente válido
    return (
      <div {...blockProps}>
        <p>
          Today the sky is {props.attributes.skyColor} and the grass is{" "}
          {props.attributes.grassColor}.
        </p>
      </div>
    );
  },
  deprecated: [
    {
      save: (props) => {
        const blockProps = useBlockProps.save();
        // Dejamos el save simple sin blockProps, que es totalmente válido
        return (
          <div {...blockProps}>
            <p>
              Today the sky is {props.attributes.skyColor} and the grass is{" "}
              {props.attributes.grassColor}.
            </p>
          </div>
        );
      },
    },
  ],
});
