import { useState, useEffect } from "react";

export default function usePostSearch(searchTerm, page) {
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [totalPages, setTotalPages] = useState(1);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchPosts = async () => {
      setLoading(true);
      setError(null);
      try {
        let endpoint = "";
        if (!searchTerm) {
          endpoint = `/wp-json/wp/v2/posts?page=${page}&per_page=5&_fields=id,title,link`;
        } else if (/^\d+$/.test(searchTerm)) {
          endpoint = `/wp-json/wp/v2/posts/${searchTerm}?_fields=id,title,link`;
        } else {
          endpoint = `/wp-json/wp/v2/posts?search=${searchTerm}&page=${page}&per_page=5&_fields=id,title,link`;
        }

        const res = await fetch(endpoint);
        if (!res.ok) {
          throw new Error("Failed to fetch posts.");
        }

        const isIdSearch = /^\d+$/.test(searchTerm);
        const json = isIdSearch ? [await res.json()] : await res.json();
        const pages = isIdSearch
          ? 1
          : parseInt(res.headers.get("X-WP-TotalPages")) || 1;

        setResults(json);
        setTotalPages(pages);
      } catch (e) {
        if ("development" === process.env.NODE_ENV) {
          // eslint-disable-next-line no-console
          console.error("usePostSearch error:", e);
        }
        setResults([]);
        setError(e.message);
      } finally {
        setLoading(false);
      }
    };

    fetchPosts();
  }, [searchTerm, page]);
  return { results, loading, totalPages, error };
}
