import React from "react";
import { render, waitFor } from "@testing-library/react";
import "@testing-library/jest-dom";
import usePostSearch from "../src/usePostSearch";

function TestComponent({ searchTerm, page }) {
  const { results, loading, totalPages } = usePostSearch(searchTerm, page);

  return (
    <div>
      <p data-testid="loading">{loading ? "true" : "false"}</p>
      <p data-testid="count">{results.length}</p>
      <p data-testid="pages">{totalPages}</p>
    </div>
  );
}

test("loads and renders post search hook state", async () => {
  global.fetch = jest.fn(() =>
    Promise.resolve({
      ok: true,
      json: () =>
        Promise.resolve([{ id: 1, title: { rendered: "Hello" }, link: "#" }]),
      headers: { get: () => "1" },
    }),
  );

  const { findByTestId } = render(<TestComponent searchTerm="test" page={1} />);

  const loadingEl = await findByTestId("loading");
  await waitFor(() => expect(loadingEl).toHaveTextContent("false"));

  expect(await findByTestId("count")).toHaveTextContent("1");
  expect(await findByTestId("pages")).toHaveTextContent("1");
});
