import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
export default function PaginationControls({ page, totalPages, setPage }) {
  return (
    <div style={{ marginTop: "1em" }}>
      <Button disabled={1 >= page} onClick={() => setPage((p) => p - 1)}>
        {__("Previous", "dmg-read-more-link")}
      </Button>
      <span style={{ margin: "0 8px" }}>
        {__("Page", "dmg-read-more-link")} {page}{" "}
        {__("of", "dmg-read-more-link")} {totalPages}
      </span>
      <Button
        disabled={page >= totalPages}
        onClick={() => setPage((p) => p + 1)}
      >
        {__("Next", "dmg-read-more-link")}
      </Button>
    </div>
  );
}
