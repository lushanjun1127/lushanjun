# 🎨 SimplePost - Minimalist WordPress Theme

<p align="center">
  <strong>A modular, lightning-fast, and distraction-free WordPress theme designed for ultimate reading and writing experience.</strong><br>
  一款模块化、极致高效且无干扰的 WordPress 极简博客主题，专为纯粹的阅读与写作而生。
</p>

---

## ✨ Features / 核心特性

### 🇬🇧 English
- **Lightning Fast & Secure**: No heavy JavaScript dependencies, native CSS driven, and rigorously optimized for security and performance.
- **Deeply Modular**: Built following WordPress best practices with separated logic and views (`admin/`, `inc/`, `template/`).
- **Micro-Blogging Support**: Built-in unique `Note` archive template for short thoughts, logs, and micro-posts.
- **Typography-Focused**: Exquisitely tuned fonts, line heights, and white spaces for immersive reading.
- **Strict Standards**: Developed with clean code compliance, verified via strict PHP_CodeSniffer rules.

### 🇨🇳 中文
- **极致轻量安全**：无臃肿的前端 JS 依赖，原生 CSS 驱动，经过严格的代码级安全与性能优化。
- **深度模块化**：严格遵循 WordPress 最佳开发标准，逻辑与视图完全分离（`/admin`, `/inc`, `/template`）。
- **微笔记支持**：内置独有的 `Note`（闪念/随笔）归档模板，完美兼容长文章与碎片化想法。
- **专注排版审美**：考究的中西文字体排版，精心调校的行距与留白，打造沉浸式阅读空间。
- **严格规范**：包含 `phpcs.xml` 配置，代码结构严谨，具备高度的工程化与洁癖感。

---

## 📂 Repository Structure / 项目结构

```text
├── admin/            # Theme custom options & backend panels / 主题自定义设置与后台管理面板
├── assets/           # Frontend stylesheets, scripts & images / 前端样式表、前端脚本及图片资源
├── inc/              # Core functions, initialization & security hooks / 核心逻辑层、主题初始化与安全钩子
├── template/         # Modular template parts and view splittings / 页面切片与模块化视图组件
├── archive-note.php  # Dedicated archive template for micro-notes / 专为短随笔/闪念设计的特色归档模板
└── phpcs.xml         # PHP_CodeSniffer configuration file / PHP 代码规范检测配置文件
```

---

## 📥 Installation / 安装指南

### 🇬🇧 English
1. Click **Code** -> **Download ZIP** at the top right of this repository.
2. Log in to your WordPress dashboard. Go to **Appearance -> Themes -> Add New Theme -> Upload Theme**.
3. Choose the downloaded `.zip` file and click **Install Now**.
4. Once installed, click **Activate** to start your elegant writing journey.

### 🇨🇳 中文
1. 点击本仓库右上角的 **Code** -> **Download ZIP** 下载主题压缩包。
2. 登录你的 WordPress 后台，前往 **外观 -> 主题 -> 添加新主题 -> 上传主题**。
3. 选择下载好的 `.zip` 文件，点击 **现在安装**。
4. 安装完成后，点击 **启用** 即可开启你的极简写作之旅。

---

## 🛠 For Developers / 开发者指南

### 🇬🇧 English
Contributions and forks are welcome! Before submitting code, please ensure it complies with the project's coding standards. You can use PHP_CodeSniffer to lint your code against our `phpcs.xml` configuration.

### 🇨🇳 中文
欢迎提交 PR 或 Fork 本项目进行二次开发！为了保持代码的严谨性，建议在提交代码或魔改前，使用 PHP_CodeSniffer 结合根目录的 `phpcs.xml` 进行代码规范校验。

---

## 📜 License / 开源协议

This project is open-sourced under the [MIT License](LICENSE).  
本项目基于 [MIT](LICENSE) 协议完全开源。
