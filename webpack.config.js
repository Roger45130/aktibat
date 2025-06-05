const Encore = require("@symfony/webpack-encore");

Encore
  // 📁 Où seront stockés les fichiers compilés
  .setOutputPath("public/build/")

  // 🌐 URL publique des fichiers compilés
  .setPublicPath("/build")

  // 🎯 Fichier d’entrée principal
  .addEntry("app", "./assets/bootstrap.js")

  // 🧩 Active le support de Sass/SCSS
  .enableSassLoader()

  // 🖼️ Copie automatique des images (facultatif mais utile)
  .copyFiles({
    from: "./assets/images",
    to: "images/[path][name].[hash:8].[ext]",
    pattern: /\.(png|jpe?g|gif|svg|webp)$/i,
  })

  // 🧠 Génère les sourcemaps pour le développement
  .enableSourceMaps(!Encore.isProduction())

  // 🏷️ Active le versioning (hash dans les noms de fichiers en prod)
  .enableVersioning(Encore.isProduction())

  // ♻️ Active un fichier runtime partagé pour toutes les pages
  .enableSingleRuntimeChunk()

  // ✅ Nettoie le dossier build à chaque compilation
  .cleanupOutputBeforeBuild()

  // 👁️ Active les notifications système (optionnel mais pratique)
  .enableBuildNotifications();

// ❌ Ne pas inclure enableStimulusBridge car Stimulus n’est pas utilisé

module.exports = Encore.getWebpackConfig();
