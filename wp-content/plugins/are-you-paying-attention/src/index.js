const { useBlockProps } = wp.blockEditor;
wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  apiVersion: 3,
  icon: "smiley",
  category: "common",
  edit: () => {
    const blockProps = useBlockProps();
    return (
      <div {...blockProps}>
        <p>Hello, this is paragraph</p>
        <h4>Hi there</h4>
      </div>
    );
  },
  save: () => {
    // Dejamos el save simple sin blockProps, que es totalmente válido
    return <h5>This is a H5</h5>;
  },
});
console.log("ok");