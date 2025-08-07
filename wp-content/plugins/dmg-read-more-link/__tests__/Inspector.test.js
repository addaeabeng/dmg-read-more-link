import React from "react";
import { render, fireEvent } from "@testing-library/react";
import "@testing-library/jest-dom";
import Inspector from "../src/Inspector";

test("renders search input and buttons and sets attributes", () => {
  const mockSetAttributes = jest.fn();

  const mockProps = {
    search: {
      searchTerm: "",
      setSearchTerm: jest.fn(),
      page: 1,
      setPage: jest.fn(),
    },
    data: {
      results: [
        { id: 1, title: { rendered: "Post One" }, link: "/post-one" },
        { id: 2, title: { rendered: "Post Two" }, link: "/post-two" },
      ],
      loading: false,
      totalPages: 2,
    },
    selectedPost: {
      postId: 2,
      postTitle: "Post Two",
      postUrl: "/post-two",
    },
    setAttributes: mockSetAttributes,
  };

  const { getByLabelText, getByText } = render(<Inspector {...mockProps} />);

  const input = getByLabelText(/Search by title or ID/i);
  fireEvent.change(input, { target: { value: "hello" } });

  expect(mockProps.search.setSearchTerm).toHaveBeenCalledWith("hello");
  expect(mockProps.search.setPage).toHaveBeenCalledWith(1);

  // Click on a post that's *not* the currently selected one
  fireEvent.click(getByText("Post One"));

  expect(mockSetAttributes).toHaveBeenCalledWith({
    postId: 1,
    postTitle: "Post One",
    postUrl: "/post-one",
  });
});

test("displays error notice when data.error is present", () => {
  const mockProps = {
    search: {
      searchTerm: "",
      setSearchTerm: jest.fn(),
      page: 1,
      setPage: jest.fn(),
    },
    data: {
      results: [],
      loading: false,
      totalPages: 1,
      error: "There was a problem fetching posts. Please try again.",
    },
    selectedPost: null,
    setAttributes: jest.fn(),
  };

  const { getByRole } = render(<Inspector {...mockProps} />);
  expect(getByRole("alert")).toHaveTextContent(
    "There was a problem fetching posts. Please try again.",
  );
});
