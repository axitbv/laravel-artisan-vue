# laravel-artisan-vue
Laravel Artisan Command for scaffolding a `Vue.js` feature application with a `Vuex` store, utilizing some best practices. 

## Installing

`composer require --dev axitbv/laravel-artisan-vue`

## Using the command

`php artisan vue:feature SomeNewFeature`

Will yield:

```
resources/js/components/some-new-feature/components/App.vue
resources/js/components/some-new-feature/index.js
resources/js/components/some-new-feature/store/actions.js
resources/js/components/some-new-feature/store/getters.js
resources/js/components/some-new-feature/store/index.js
resources/js/components/some-new-feature/store/mutation_types.js
resources/js/components/some-new-feature/store/mutations.js
resources/js/components/some-new-feature/store/state.js
```

## Using the generated files

> The idea here is to Code Split each Vue.js Feature App into its own bundle. This bundle is then loaded into the view that will service the feature. One view, one page specific bundle, one store, one service, one entrypoint, one container component.

1. Make sure that the entrypoint `resources/js/components/some-new-feature/index.js` is built to its own bundle.

Example `webpack.mix.js` configuration:
```js
const mix = require("laravel-mix");

mix.js("resources/js/app.js", "public/js")
    .js("resources/js/components/some-new-feature/index.js", "public/js/some-new-feature.js")
    .sass("resources/sass/app.scss", "public/css/all.css");

mix.extract();

if (mix.config.production) {
    mix.version();
}
```

2. One your view, make sure that you load that js, after the main scripts (`manifest.js`, `vendor.js` and `app.js`).

```html
@section('after_scripts')
<script src="{{ url (mix('/js/some-new-feature.js')) }}" type="text/javascript"></script>
@endsection
```

If you don't have a section like that yet, modify your `layout/app.blade.php`. Just make sure that the app feature bundle is loaded after the other scripts.

3. Add a div with the appropriate `id`, in which the feature app will boot.

```html
<div id="js-vue-some-new-feature"></div>
```

4. **Optional**: If you pass any data from the controller to the feature app, you can add one or more `data-*` attributes on the `<div>` tag.

You can pass *any* type of data to one or more `data`-attributes, but one of the most common examples is to pass some sort of endpoint for the app to communicate with. For example, in a Blog Editor feature app, one could pass a full path to a post like so:

```html
<div id="js-vue-blog-editor" data-endpoint="/api/v1/posts/10"></div>
```

And have an action call the api and retrieve the data.

Another way would be to simply get the post data from the Controller and pass it as a prop:

```html
<div id="js-vue-blog-editor" data-post="{{ $post }}"></div>
```

Or yet another way:

```html
<div id="js-vue-blog-editor" data-endpoint="/api/v1/posts" data-load="10223"></div>
```

It's up to you.

## Laravel Default Vue.js Setup Caveats

The way that Laravel ships Vue.js (with a frontend preset) assumes that would want the following to be true:

1. You want to load Vue-components anywhere on the page
2. You want to have any and all Vue-components you have, loaded into memory and ready to go, with each and every page request.
3. If your Vue-components have Vuex-stores, it will be merged into one global single state tree. This means that, if you follow the load-everything-all-the-time-all-at-once method, that your Root instance will have a combined state of everything, even when a feature is not necessarily shown or used on a page. 

This is achieved by having an encapsulating `<div id="app"></div>` in the `layouts/app.blade.php` file that is scaffolded by the frontend preset.

Additionally, the `app.js` will boot a Vue.js Root instance inside of that encapsulating `<div>`. The provided `ExampleComponent` is also loaded into memory, and there is a code snippet that can recursively load *all* Vue files.

> ## **If you are building large applications, with large features, that have numerous subcomponents and Vuex stores, this will become unsustainable very quickly!**

**What you want to achieve is the following:**

* For any Vue-components that need to be globally available on each and every page view, register it in the `app.js`. Stuff like Navigational components, such as navbars, menu's, etc.

* Instead of having One Big Vue Instance: Create multiple Vue-instances (Roots) for the various sections of your layout (Navbar section, sidebar, control sidebar, footer, etc.) all with uniquely identifying `id`'s, preferably properly namespaced, like `#navbar-js-vue`, `#sidebar-js-vue`.

* Create one page specific bundle for each feature view of your app, that will have its own entrypoint, its own container, its own Vue.js Root instance. **This is what this package will provide!**

The result is that you will be loading only the stuff that is required for the feature that is served by your application's view into memory. The additional benefit is that when you have multiple Vuex-enabled stores, you will not get a global single state tree: the state is confined to the Root of your feature app. Lastly, the Vue.js devtools plays very nice with multiple roots as well.

## Credits

This was heavily inspired by GitLab's Front End Development Guidelines:

- https://docs.gitlab.com/ee/development/fe_guide/vue.html
- https://docs.gitlab.com/ee/development/fe_guide/vuex.html

## License

MIT
