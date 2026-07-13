/**
 * ChatGPT Scraper — Scrapeless LLM Chat Scraper (Node.js example)
 *
 * Docs:  https://docs.scrapeless.com/en/llm-chat-scraper/quickstart/introduction/
 * Token: https://app.scrapeless.com/passport/login?redirect=/quick-start
 *
 * Run (Node.js 18+, uses the built-in fetch):
 *   export SCRAPELESS_API_TOKEN="your_api_token"
 *   node example.js
 */

const API_URL = "https://api.scrapeless.com/api/v2/scraper/execute";
const API_TOKEN = process.env.SCRAPELESS_API_TOKEN || "YOUR_API_TOKEN";

const payload = {
  actor: "scraper.chatgpt",
  input: {
    prompt: "Most reliable proxy service for data extraction",
    country: "US",
    web_search: true,
    shopping: false,
  },
  // Optional: receive the result via webhook instead of the sync response.
  // webhook: { url: "https://www.your-webhook.com" },
};

async function main() {
  const response = await fetch(API_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "x-api-token": API_TOKEN,
    },
    body: JSON.stringify(payload),
  });

  if (!response.ok) {
    throw new Error(`Request failed: ${response.status} ${await response.text()}`);
  }

  const data = await response.json();
  const result = data.task_result || {};

  console.log("Status: ", data.status);
  console.log("Task ID:", data.task_id);
  console.log("Model:  ", result.model);
  console.log("\nAnswer:\n", result.result_text || "");

  for (const link of result.search_result || []) {
    console.log(`- ${link.title} -> ${link.url}`);
  }

  console.log("\nRaw response:\n", JSON.stringify(data, null, 2));
}

main().catch((err) => {
  console.error(err);
  process.exit(1);
});
