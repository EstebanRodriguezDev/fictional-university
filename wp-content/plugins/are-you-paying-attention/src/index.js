import "./index.scss";
import { TextControl, Flex, FlexBlock, FlexItem, Button, Icon } from "@wordpress/components";
import { starEmpty } from "@wordpress/icons";
wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  apiVersion: 3,
  icon: "smiley",
  category: "common",
  attributes: {
    skyColor: { type: "string" },
    grassColor: { type: "string" },
  },
  edit: EditComponent,
  save: (props) => {
    return null;
  }
});
function EditComponent(props) {
  const blockProps = wp.blockEditor.useBlockProps({ className: "paying-attention-edit-block" });
  function updateSkyColor(event) {
    props.setAttributes({ skyColor: event.target.value });
  }
  function updateGrassColor(event) {
    props.setAttributes({ grassColor: event.target.value });
  }
  return (
    <div {...blockProps}>
      <TextControl label="Question:" />
      <p>Answers:</p>
      <Flex>
        <FlexBlock>
          <TextControl />
        </FlexBlock>
        <FlexItem>
          <Button>
            <Icon icon={starEmpty} style={{ color: '#ffb900' }} />
          </Button>
        </FlexItem>
        <FlexItem>
          <Button>Delete</Button>
        </FlexItem>
      </Flex>
    </div>
  );
}