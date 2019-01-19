module.exports = {
  root: true,
  env: {
    node: true
  },
  extends: ["plugin:vue/essential", "@vue/prettier"],
  rules: {
    "no-console": process.env.NODE_ENV === "production" ? "error" : "off",
    "no-debugger": process.env.NODE_ENV === "production" ? "error" : "off",
    // インデントはスペース２つ.
    indent: [0, "space"],
    // 未使用の変数はエラー出さない.
    "no-unused-vars": [
      "off",
      { vars: "all", args: "after-used", ignoreRestSiblings: false }
    ]
  },
  parserOptions: {
    parser: "babel-eslint"
  }
};
