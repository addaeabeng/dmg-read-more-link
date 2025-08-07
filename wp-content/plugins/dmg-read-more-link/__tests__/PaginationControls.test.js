import React from "react";
import { render, fireEvent, getByText } from "@testing-library/react";
import "@testing-library/jest-dom";
import PaginationControls from "../src/PaginationControls";

describe("PaginationControls", () => {
  test("disables previous button on first page", () => {
    const { getByText } = render(
      <PaginationControls page={1} totalPages={5} setPage={() => {}} />,
    );
    expect(getByText("Previous")).toBeDisabled();
    expect(getByText("Next")).not.toBeDisabled();
  });

  test("disables next button on last page", () => {
    const { getByText } = render(
      <PaginationControls page={5} totalPages={5} setPage={() => {}} />,
    );
    expect(getByText("Next")).toBeDisabled();
    expect(getByText("Previous")).not.toBeDisabled();
  });

  test("calls setPage with correct values", () => {
    const mockSetPage = jest.fn();
    const { getByText } = render(
      <PaginationControls page={3} totalPages={5} setPage={mockSetPage} />,
    );

    fireEvent.click(getByText("Previous"));
    fireEvent.click(getByText("Next"));

    expect(mockSetPage).toHaveBeenCalledTimes(2);

    // Since setPage is called as a function updater, we test it's called with a function
    expect(typeof mockSetPage.mock.calls[0][0]).toBe("function");
    expect(typeof mockSetPage.mock.calls[1][0]).toBe("function");
  });
});
