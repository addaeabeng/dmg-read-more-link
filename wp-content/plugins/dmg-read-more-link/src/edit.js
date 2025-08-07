import { useBlockProps } from "@wordpress/block-editor";

import { useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import usePostSearch from "./usePostSearch";
import Inspector from "./Inspector";

export default function Edit({ attributes, setAttributes }) {
  const [searchTerm, setSearchTerm] = useState("");
  const [page, setPage] = useState(1);
  const { results, loading, totalPages, error } = usePostSearch(
    searchTerm,
    page,
  );

  const blockProps = useBlockProps();
  const selectedPost = {
    postId: attributes.postId,
    postTitle: attributes.postTitle,
    postUrl: attributes.postUrl,
  };

  const search = { searchTerm, setSearchTerm, page, setPage };
  const data = { results, loading, totalPages, error };

  return (
    <>
      <Inspector
        search={search}
        data={data}
        selectedPost={selectedPost}
        setAttributes={setAttributes}
      />
      <div {...blockProps}>
        {selectedPost.postId ? (
          <p className={"dmg-read-more"}>
            {__("Read More:", "dmg-read-more-link")}{" "}
            <a
              href={selectedPost.postUrl}
              target={"_blank"}
              rel={"noopener noreferrer"}
            >
              {selectedPost.postTitle}
            </a>
          </p>
        ) : (
          <p className={"dmg-read-more"}>
            {__("No post selected.", "dmg-read-more-link")}
          </p>
        )}
      </div>
    </>
  );
}
