import { useBlockProps } from "@wordpress/block-editor";
import { __ } from "@wordpress/i18n";
export default function save({ attributes }) {
  const { postUrl, postTitle } = attributes;
  return (
    <p className={"dmg-read-more"} {...useBlockProps.save()}>
      {__("Read More:", "dmg-read-more-link")} <a href={postUrl}>{postTitle}</a>
    </p>
  );
}
