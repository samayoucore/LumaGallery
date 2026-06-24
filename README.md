# Luma Gallery

## Русская версия

**Luma Gallery** — концептуальная онлайн-галерея современного искусства, разработанная на WordPress как portfolio pet-project.

Проект объединяет произведения искусства, профили художников, выставки и редакционный журнал в едином цифровом пространстве. Сайт имитирует полноценную платформу для просмотра, сохранения, покупки и публикации произведений искусства.

Luma Gallery разработана без использования готовых тем и визуальных конструкторов. В основе проекта лежат собственная WordPress-тема, отдельный функциональный плагин, ручная SCSS/JavaScript-сборка и набор интерактивных пользовательских сценариев.

> **Демонстрационный проект.** На сайте не проводятся реальные платежи, не используются платные или внешние AI API, а данные корзины, заказов, избранного и кабинета художника сохраняются локально в браузере через `localStorage`.

## Концепция

Luma Gallery представляет собой премиальную цифровую арт-галерею с editorial-визуальным стилем.

Проект построен вокруг идеи единого пространства, в котором пользователь может:

- изучать произведения искусства;
- знакомиться с художниками;
- просматривать текущие и будущие выставки;
- читать редакционные материалы;
- сохранять понравившиеся работы;
- оформлять демонстрационные заказы;
- взаимодействовать с персональными рекомендациями;
- использовать отдельное рабочее пространство художника.

Визуальное направление сочетает крупную типографику, тонкие линии, почти прямые углы, большие фоновые надписи и сдержанную цветовую палитру с ярким акцентом.

## Основные возможности

### Просмотр и поиск искусства

- каталог произведений с фильтрацией;
- страницы художников и выставок;
- переключение между различными вариантами отображения каталога;
- редакционный журнал с отдельными публикациями и тематическими категориями;
- собственная страница поиска;
- полноэкранный режим просмотра Gallery Walk;
- функция View in Room для демонстрации произведения в интерьере.

### Избранное и демонстрационная покупка

- сохранение произведений, художников и выставок в избранное;
- отдельная страница с сохранёнными материалами;
- демонстрационная корзина;
- оформление заказа с валидацией полей;
- создание локального номера заказа;
- история оформленных заказов;
- личный кабинет пользователя;
- отображение недавно просмотренных произведений.

Все данные этого раздела сохраняются локально в браузере. Реальные платежи и передача платёжных данных не выполняются.

### Кабинет художника

В проекте реализован демонстрационный Artist Studio — рабочее пространство для художника.

В нём можно:

- добавлять и редактировать произведения;
- просматривать изменения в режиме живого предпросмотра;
- создавать публикации для журнала;
- управлять локальными данными;
- генерировать описания, истории, теги, SEO-тексты и материалы для социальных сетей.

Контент кабинета существует только в браузере и не отправляется на внешний сервер.

### AI-функции

В Luma Gallery реализованы функции, имитирующие работу искусственного интеллекта:

- **AI Curator** формирует подборку произведений на основе текстового описания пространства, настроения или предпочтений пользователя;
- **AI Assistant** создаёт описания, теги, истории и маркетинговые тексты для художника.

Эти функции работают на основе заранее подготовленных правил, фильтров, шаблонов и наборов фраз. Внешние языковые модели и платные AI-сервисы не используются.

## Архитектура проекта

Проект разделён на три основных уровня:

- **собственная WordPress-тема** отвечает за интерфейс, шаблоны страниц, стили, анимации и клиентскую логику;
- **плагин Luma Core** содержит типы записей, таксономии, демонстрационные данные и инструменты для создания контента;
- **локальный демонстрационный слой** хранит избранное, корзину, заказы, данные аккаунта и Artist Studio через `localStorage`.

Такое разделение позволяет отделить визуальную часть сайта от бизнес-логики и контента.

## Технологии

В проекте используются:

- WordPress;
- PHP;
- собственная WordPress-тема;
- собственный плагин Luma Core;
- SCSS;
- Gulp;
- Vanilla JavaScript;
- `localStorage`;
- классические шаблоны WordPress;
- адаптивная и доступная вёрстка.

WooCommerce не является обязательной зависимостью. Проект может работать как полноценная демонстрация без его установки.

## Цель проекта

Luma Gallery создавалась как portfolio pet-project, демонстрирующий:

- разработку собственной темы WordPress с нуля;
- создание отдельного функционального WordPress-плагина;
- разделение интерфейса и бизнес-логики;
- работу с пользовательскими типами записей и таксономиями;
- создание сложного многостраничного интерфейса;
- разработку интерактивных сценариев без готовых конструкторов;
- работу с SCSS и собственной frontend-сборкой;
- создание адаптивного и доступного интерфейса;
- реализацию демонстрационной e-commerce-логики;
- проектирование личного кабинета и рабочего пространства художника;
- использование AI-assisted подхода в процессе разработки.

Главная задача проекта — показать возможность создания цельного и визуально выразительного WordPress-продукта без готовых тем, page builder-инструментов, внешних AI API и обязательной зависимости от WooCommerce.

---

## English Version

**Luma Gallery** is a conceptual online contemporary art gallery built with WordPress as a portfolio pet project.

The project brings artworks, artist profiles, exhibitions, and an editorial journal together within a single digital space. It simulates a complete platform for discovering, saving, purchasing, and publishing artwork.

Luma Gallery was developed without pre-built themes or visual page builders. The project is based on a custom WordPress theme, a separate functionality plugin, a hand-built SCSS and JavaScript pipeline, and a collection of interactive user flows.

> **Portfolio demo.** No real payments are processed, no paid or external AI APIs are used, and all cart, order, favorites, and artist workspace data is stored locally in the browser through `localStorage`.

## Concept

Luma Gallery is designed as a premium digital art gallery with an editorial visual direction.

The project is built around the idea of a unified space where users can:

- discover artworks;
- explore artist profiles;
- browse current and upcoming exhibitions;
- read editorial publications;
- save their favorite works;
- place demonstration orders;
- receive personalized selections;
- use a dedicated artist workspace.

The visual direction combines oversized typography, thin borders, near-square corners, large background words, and a restrained color palette with a bright accent color.

## Main Features

### Art Discovery and Search

- artwork catalog with filtering;
- artist and exhibition pages;
- multiple catalog layout modes;
- editorial journal with individual articles and topic categories;
- custom search results page;
- immersive Gallery Walk mode;
- View in Room functionality for displaying an artwork inside an interior.

### Favorites and Demo Purchases

- saving artworks, artists, and exhibitions;
- a dedicated favorites page;
- demonstration shopping cart;
- checkout flow with inline validation;
- locally generated order numbers;
- demo order history;
- customer account dashboard;
- recently viewed artworks.

All data in this section is stored locally in the browser. No real payments are processed, and no payment information is transmitted.

### Artist Workspace

The project includes a demonstration Artist Studio that acts as a workspace for artists.

Artists can:

- add and edit artworks;
- view changes through a live preview;
- create journal publications;
- manage locally stored content;
- generate descriptions, stories, tags, SEO copy, and social media text.

Artist Studio content exists only inside the browser and is not sent to an external server.

### AI-style Features

Luma Gallery contains several features that simulate artificial intelligence:

- **AI Curator** creates an artwork selection based on a written description of the user’s space, mood, or preferences;
- **AI Assistant** generates descriptions, tags, stories, and promotional content for artists.

These features are powered by deterministic rules, filters, templates, and phrase collections. No external language models or paid AI services are used.

## Project Architecture

The project is divided into three main layers:

- the **custom WordPress theme** handles the interface, page templates, styling, animations, and client-side logic;
- the **Luma Core plugin** contains custom post types, taxonomies, demonstration data, and content-management tools;
- the **local demonstration layer** stores favorites, cart data, orders, account information, and Artist Studio content through `localStorage`.

This separation keeps the presentation layer independent from business and content logic.

## Technologies

The project uses:

- WordPress;
- PHP;
- a custom WordPress theme;
- the custom Luma Core plugin;
- SCSS;
- Gulp;
- Vanilla JavaScript;
- `localStorage`;
- classic WordPress templates;
- responsive and accessible frontend development.

WooCommerce is not a required dependency. The project can operate as a complete demonstration without it.

## Project Goal

Luma Gallery was created as a portfolio pet project that demonstrates:

- developing a custom WordPress theme from scratch;
- building a separate functionality plugin;
- separating presentation from business logic;
- working with custom post types and taxonomies;
- creating a complex multi-page interface;
- developing interactive functionality without page builders;
- working with SCSS and a custom frontend build pipeline;
- building a responsive and accessible interface;
- implementing a demonstration e-commerce flow;
- designing a customer account and artist workspace;
- using an AI-assisted development workflow.

The main purpose of the project is to demonstrate how a cohesive and visually expressive WordPress product can be developed without pre-built themes, page builders, external AI APIs, or a required WooCommerce dependency.
