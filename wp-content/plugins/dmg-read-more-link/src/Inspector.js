import { InspectorControls } from "@wordpress/block-editor";
import {
  PanelBody,
  TextControl,
  Button,
  Spinner,
  Notice,
} from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import PaginationControls from "./PaginationControls";

export default function Inspector({
  search,
  data,
  selectedPost,
  setAttributes,
}) {
  const { searchTerm, setSearchTerm, page, setPage } = search;
  const { results, loading, totalPages, error } = data;

  return (
    <InspectorControls>
      <PanelBody
        title={__("Select a Post", "dmg-read-more-link")}
        initialOpen={true}
      >
        <TextControl
          label={__("Search by title or ID", "dmg-read-more-link")}
          value={searchTerm}
          onChange={(val) => {
            setSearchTerm(val);
            setPage(1);
          }}
        />
        {loading && <Spinner />}
        {!loading && error && (
          <Notice status="error" isDismissible={false}>
            {__(
              "There was a problem fetching posts. Please try again.",
              "dmg-read-more-link",
            )}
          </Notice>
        )}
        {!loading &&
          0 < results.length &&
          results.map((post) => (
            <Button
              key={post.id}
              variant={
                post.id === selectedPost?.postId ? "primary" : "secondary"
              }
              onClick={() =>
                setAttributes({
                  postId: post.id,
                  postTitle: post.title.rendered,
                  postUrl: post.link,
                })
              }
              style={{ display: "block", marginBottom: "6px" }}
            >
              {post.title.rendered}
            </Button>
          ))}
        <PaginationControls
          page={page}
          totalPages={totalPages}
          setPage={setPage}
        />
      </PanelBody>
    </InspectorControls>
  );
}
