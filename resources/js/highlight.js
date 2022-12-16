import hljs from "highlight.js/lib/core"
import bash from "highlight.js/lib/languages/bash"
import graphql from "highlight.js/lib/languages/graphql"
import json from "highlight.js/lib/languages/json"

hljs.registerLanguage("bash", bash);
hljs.registerLanguage("graphql", graphql);
hljs.registerLanguage("json", json);

hljs.highlightAll()
