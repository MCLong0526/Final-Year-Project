// vite.config.js
import vue from "file:///var/www/html/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import vueJsx from "file:///var/www/html/node_modules/@vitejs/plugin-vue-jsx/dist/index.mjs";
import laravel from "file:///var/www/html/node_modules/laravel-vite-plugin/dist/index.js";
import { fileURLToPath } from "node:url";
import AutoImport from "file:///var/www/html/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///var/www/html/node_modules/unplugin-vue-components/dist/vite.js";
import { defineConfig } from "file:///var/www/html/node_modules/vite/dist/node/index.js";
import Pages from "file:///var/www/html/node_modules/vite-plugin-pages/dist/index.js";
import Layouts from "file:///var/www/html/node_modules/vite-plugin-vue-layouts/dist/index.mjs";
import vuetify from "file:///var/www/html/node_modules/vite-plugin-vuetify/dist/index.mjs";
import svgLoader from "file:///var/www/html/node_modules/vite-svg-loader/index.js";
var __vite_injected_original_import_meta_url = "file:///var/www/html/vite.config.js";
var vite_config_default = defineConfig({
  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        },
        compilerOptions: {
          // treat all tags with a dash as custom elements
          isCustomElement: (tag) => tag.startsWith("emoji-")
        }
      }
    }),
    vueJsx(),
    laravel({
      input: ["resources/js/main.js"],
      refresh: true
    }),
    // Docs: https://github.com/vuetifyjs/vuetify-loader/tree/master/packages/vite-plugin
    vuetify({
      styles: {
        configFile: "resources/styles/variables/_vuetify.scss"
      }
    }),
    Pages({
      dirs: ["resources/js/pages"]
    }),
    Layouts({
      layoutsDirs: "resources/js/layouts/"
    }),
    Components({
      dirs: ["resources/js/@core/components", "resources/js/components"],
      dts: true
    }),
    // Docs: https://github.com/antfu/unplugin-auto-import#unplugin-auto-import
    AutoImport({
      imports: ["vue", "vue-router", "@vueuse/core", "@vueuse/math", "pinia"],
      vueTemplate: true,
      // ℹ️ Disabled to avoid confusion & accidental usage
      ignore: ["useCookies", "useStorage"],
      eslintrc: {
        enabled: true,
        filepath: "./.eslintrc-auto-import.json"
      }
    }),
    svgLoader()
  ],
  define: { "process.env": {} },
  resolve: {
    alias: {
      "@core-scss": fileURLToPath(new URL("./resources/styles/@core", __vite_injected_original_import_meta_url)),
      "@": fileURLToPath(new URL("./resources/js", __vite_injected_original_import_meta_url)),
      "@core": fileURLToPath(new URL("./resources/js/@core", __vite_injected_original_import_meta_url)),
      "@layouts": fileURLToPath(new URL("./resources/js/@layouts", __vite_injected_original_import_meta_url)),
      "@images": fileURLToPath(new URL("./resources/images/", __vite_injected_original_import_meta_url)),
      "@styles": fileURLToPath(new URL("./resources/styles/", __vite_injected_original_import_meta_url)),
      "@configured-variables": fileURLToPath(new URL("./resources/styles/variables/_template.scss", __vite_injected_original_import_meta_url))
    }
  },
  build: {
    chunkSizeWarningLimit: 5e3
  },
  optimizeDeps: {
    exclude: ["vuetify"],
    entries: [
      "./resources/js/**/*.vue"
    ]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvdmFyL3d3dy9odG1sXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCIvdmFyL3d3dy9odG1sL3ZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy92YXIvd3d3L2h0bWwvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgdnVlIGZyb20gJ0B2aXRlanMvcGx1Z2luLXZ1ZSdcbmltcG9ydCB2dWVKc3ggZnJvbSAnQHZpdGVqcy9wbHVnaW4tdnVlLWpzeCdcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nXG5pbXBvcnQgeyBmaWxlVVJMVG9QYXRoIH0gZnJvbSAnbm9kZTp1cmwnXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJ1xuaW1wb3J0IENvbXBvbmVudHMgZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZSdcbmltcG9ydCB7IGRlZmluZUNvbmZpZyB9IGZyb20gJ3ZpdGUnXG5pbXBvcnQgUGFnZXMgZnJvbSAndml0ZS1wbHVnaW4tcGFnZXMnXG5pbXBvcnQgTGF5b3V0cyBmcm9tICd2aXRlLXBsdWdpbi12dWUtbGF5b3V0cydcbmltcG9ydCB2dWV0aWZ5IGZyb20gJ3ZpdGUtcGx1Z2luLXZ1ZXRpZnknXG5pbXBvcnQgc3ZnTG9hZGVyIGZyb20gJ3ZpdGUtc3ZnLWxvYWRlcidcblxuLy8gaHR0cHM6Ly92aXRlanMuZGV2L2NvbmZpZy9cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gIHBsdWdpbnM6IFt2dWUoe1xuICAgIHRlbXBsYXRlOiB7XG4gICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgaW5jbHVkZUFic29sdXRlOiBmYWxzZSxcbiAgICAgIH0sXG4gICAgICBjb21waWxlck9wdGlvbnM6IHtcbiAgICAgICAgLy8gdHJlYXQgYWxsIHRhZ3Mgd2l0aCBhIGRhc2ggYXMgY3VzdG9tIGVsZW1lbnRzXG4gICAgICAgIGlzQ3VzdG9tRWxlbWVudDogKHRhZykgPT4gdGFnLnN0YXJ0c1dpdGgoXCJlbW9qaS1cIiksXG4gICAgICB9LFxuICAgIFxuICAgIH0sXG4gIH0pLFxuICB2dWVKc3goKSxcbiAgbGFyYXZlbCh7XG4gICAgaW5wdXQ6IFsncmVzb3VyY2VzL2pzL21haW4uanMnXSxcbiAgICByZWZyZXNoOiB0cnVlLFxuICB9KSwgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL3Z1ZXRpZnlqcy92dWV0aWZ5LWxvYWRlci90cmVlL21hc3Rlci9wYWNrYWdlcy92aXRlLXBsdWdpblxuICB2dWV0aWZ5KHtcbiAgICBzdHlsZXM6IHtcbiAgICAgIGNvbmZpZ0ZpbGU6ICdyZXNvdXJjZXMvc3R5bGVzL3ZhcmlhYmxlcy9fdnVldGlmeS5zY3NzJyxcbiAgICB9LFxuICB9KSxcbiAgUGFnZXMoe1xuICAgIGRpcnM6IFsncmVzb3VyY2VzL2pzL3BhZ2VzJ10sXG4gIH0pLFxuICBMYXlvdXRzKHtcbiAgICBsYXlvdXRzRGlyczogJ3Jlc291cmNlcy9qcy9sYXlvdXRzLycsXG4gIH0pLFxuICBDb21wb25lbnRzKHtcbiAgICBkaXJzOiBbJ3Jlc291cmNlcy9qcy9AY29yZS9jb21wb25lbnRzJywgJ3Jlc291cmNlcy9qcy9jb21wb25lbnRzJ10sXG4gICAgZHRzOiB0cnVlLFxuICB9KSwgLy8gRG9jczogaHR0cHM6Ly9naXRodWIuY29tL2FudGZ1L3VucGx1Z2luLWF1dG8taW1wb3J0I3VucGx1Z2luLWF1dG8taW1wb3J0XG4gIEF1dG9JbXBvcnQoe1xuICAgIGltcG9ydHM6IFsndnVlJywgJ3Z1ZS1yb3V0ZXInLCAnQHZ1ZXVzZS9jb3JlJywgJ0B2dWV1c2UvbWF0aCcsICdwaW5pYSddLFxuICAgIHZ1ZVRlbXBsYXRlOiB0cnVlLFxuXG4gICAgLy8gXHUyMTM5XHVGRTBGIERpc2FibGVkIHRvIGF2b2lkIGNvbmZ1c2lvbiAmIGFjY2lkZW50YWwgdXNhZ2VcbiAgICBpZ25vcmU6IFsndXNlQ29va2llcycsICd1c2VTdG9yYWdlJ10sXG4gICAgZXNsaW50cmM6IHtcbiAgICAgIGVuYWJsZWQ6IHRydWUsXG4gICAgICBmaWxlcGF0aDogJy4vLmVzbGludHJjLWF1dG8taW1wb3J0Lmpzb24nLFxuICAgIH0sXG4gIH0pLFxuICBzdmdMb2FkZXIoKV0sXG4gIGRlZmluZTogeyAncHJvY2Vzcy5lbnYnOiB7fSB9LFxuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgICdAY29yZS1zY3NzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9zdHlsZXMvQGNvcmUnLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9qcycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0Bjb3JlJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9qcy9AY29yZScsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0BsYXlvdXRzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9qcy9AbGF5b3V0cycsIGltcG9ydC5tZXRhLnVybCkpLFxuICAgICAgJ0BpbWFnZXMnOiBmaWxlVVJMVG9QYXRoKG5ldyBVUkwoJy4vcmVzb3VyY2VzL2ltYWdlcy8nLCBpbXBvcnQubWV0YS51cmwpKSxcbiAgICAgICdAc3R5bGVzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9zdHlsZXMvJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgICAnQGNvbmZpZ3VyZWQtdmFyaWFibGVzJzogZmlsZVVSTFRvUGF0aChuZXcgVVJMKCcuL3Jlc291cmNlcy9zdHlsZXMvdmFyaWFibGVzL190ZW1wbGF0ZS5zY3NzJywgaW1wb3J0Lm1ldGEudXJsKSksXG4gICAgfSxcbiAgfSxcbiAgYnVpbGQ6IHtcbiAgICBjaHVua1NpemVXYXJuaW5nTGltaXQ6IDUwMDAsXG4gICAgXG4gIH0sXG4gIG9wdGltaXplRGVwczoge1xuICAgIGV4Y2x1ZGU6IFsndnVldGlmeSddLFxuICAgIGVudHJpZXM6IFtcbiAgICAgICcuL3Jlc291cmNlcy9qcy8qKi8qLnZ1ZScsXG4gICAgXSxcbiAgfSxcbn0pXG4iXSwKICAibWFwcGluZ3MiOiAiO0FBQXlOLE9BQU8sU0FBUztBQUN6TyxPQUFPLFlBQVk7QUFDbkIsT0FBTyxhQUFhO0FBQ3BCLFNBQVMscUJBQXFCO0FBQzlCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLFNBQVMsb0JBQW9CO0FBQzdCLE9BQU8sV0FBVztBQUNsQixPQUFPLGFBQWE7QUFDcEIsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sZUFBZTtBQVY0RyxJQUFNLDJDQUEyQztBQWFuTCxJQUFPLHNCQUFRLGFBQWE7QUFBQSxFQUMxQixTQUFTO0FBQUEsSUFBQyxJQUFJO0FBQUEsTUFDWixVQUFVO0FBQUEsUUFDUixvQkFBb0I7QUFBQSxVQUNsQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNuQjtBQUFBLFFBQ0EsaUJBQWlCO0FBQUE7QUFBQSxVQUVmLGlCQUFpQixDQUFDLFFBQVEsSUFBSSxXQUFXLFFBQVE7QUFBQSxRQUNuRDtBQUFBLE1BRUY7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELE9BQU87QUFBQSxJQUNQLFFBQVE7QUFBQSxNQUNOLE9BQU8sQ0FBQyxzQkFBc0I7QUFBQSxNQUM5QixTQUFTO0FBQUEsSUFDWCxDQUFDO0FBQUE7QUFBQSxJQUNELFFBQVE7QUFBQSxNQUNOLFFBQVE7QUFBQSxRQUNOLFlBQVk7QUFBQSxNQUNkO0FBQUEsSUFDRixDQUFDO0FBQUEsSUFDRCxNQUFNO0FBQUEsTUFDSixNQUFNLENBQUMsb0JBQW9CO0FBQUEsSUFDN0IsQ0FBQztBQUFBLElBQ0QsUUFBUTtBQUFBLE1BQ04sYUFBYTtBQUFBLElBQ2YsQ0FBQztBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1QsTUFBTSxDQUFDLGlDQUFpQyx5QkFBeUI7QUFBQSxNQUNqRSxLQUFLO0FBQUEsSUFDUCxDQUFDO0FBQUE7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNULFNBQVMsQ0FBQyxPQUFPLGNBQWMsZ0JBQWdCLGdCQUFnQixPQUFPO0FBQUEsTUFDdEUsYUFBYTtBQUFBO0FBQUEsTUFHYixRQUFRLENBQUMsY0FBYyxZQUFZO0FBQUEsTUFDbkMsVUFBVTtBQUFBLFFBQ1IsU0FBUztBQUFBLFFBQ1QsVUFBVTtBQUFBLE1BQ1o7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELFVBQVU7QUFBQSxFQUFDO0FBQUEsRUFDWCxRQUFRLEVBQUUsZUFBZSxDQUFDLEVBQUU7QUFBQSxFQUM1QixTQUFTO0FBQUEsSUFDUCxPQUFPO0FBQUEsTUFDTCxjQUFjLGNBQWMsSUFBSSxJQUFJLDRCQUE0Qix3Q0FBZSxDQUFDO0FBQUEsTUFDaEYsS0FBSyxjQUFjLElBQUksSUFBSSxrQkFBa0Isd0NBQWUsQ0FBQztBQUFBLE1BQzdELFNBQVMsY0FBYyxJQUFJLElBQUksd0JBQXdCLHdDQUFlLENBQUM7QUFBQSxNQUN2RSxZQUFZLGNBQWMsSUFBSSxJQUFJLDJCQUEyQix3Q0FBZSxDQUFDO0FBQUEsTUFDN0UsV0FBVyxjQUFjLElBQUksSUFBSSx1QkFBdUIsd0NBQWUsQ0FBQztBQUFBLE1BQ3hFLFdBQVcsY0FBYyxJQUFJLElBQUksdUJBQXVCLHdDQUFlLENBQUM7QUFBQSxNQUN4RSx5QkFBeUIsY0FBYyxJQUFJLElBQUksK0NBQStDLHdDQUFlLENBQUM7QUFBQSxJQUNoSDtBQUFBLEVBQ0Y7QUFBQSxFQUNBLE9BQU87QUFBQSxJQUNMLHVCQUF1QjtBQUFBLEVBRXpCO0FBQUEsRUFDQSxjQUFjO0FBQUEsSUFDWixTQUFTLENBQUMsU0FBUztBQUFBLElBQ25CLFNBQVM7QUFBQSxNQUNQO0FBQUEsSUFDRjtBQUFBLEVBQ0Y7QUFDRixDQUFDOyIsCiAgIm5hbWVzIjogW10KfQo=
