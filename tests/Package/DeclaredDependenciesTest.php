<?php

namespace Innoboxrr\LaravelOptions\Tests\Package;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Que cada paquete del que el codigo importa clases esta declarado.
 *
 * Aqui se colaba el fallo: Option usa MetaOperations de innoboxrr/traits y las
 * peticiones usan innoboxrr/search-surge, pero ninguno estaba en require. Los
 * tests pasaban porque llegaban de rebote por larapack-generator, que es de
 * desarrollo; una aplicacion que instalaba el paquete no los tenia.
 *
 * Un paquete que otro reemplaza cuenta como declarado: laravel/framework
 * reemplaza a illuminate/support.
 */
final class DeclaredDependenciesTest extends TestCase
{
    #[Test]
    public function cada_paquete_importado_esta_en_require_o_suggest(): void
    {
        $root = dirname(__DIR__, 2);
        $composer = json_decode((string) file_get_contents($root . '/composer.json'), true);
        $declared = array_keys(($composer['require'] ?? []) + ($composer['suggest'] ?? []));
        $packages = $this->installedPackages($root);

        $missing = [];

        foreach ($this->importedClasses($root) as $class => $file) {
            $package = $this->owner($class, $packages);

            if ($package === null || $package['name'] === $composer['name']) {
                continue;
            }

            $provides = array_merge([$package['name']], array_keys($package['replace'] ?? []));

            if (array_intersect($provides, $declared) === []) {
                $missing[$package['name']][] = "{$class} ({$file})";
            }
        }

        $this->assertSame([], $missing, 'Paquetes importados que composer.json no declara.');
    }

    /**
     * @return array<string, string> clase => archivo
     */
    private function importedClasses(string $root): array
    {
        $classes = [];

        foreach (['src', 'database', 'routes', 'config'] as $dir) {
            if (! is_dir("{$root}/{$dir}")) {
                continue;
            }

            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator("{$root}/{$dir}", \FilesystemIterator::SKIP_DOTS));

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                // Solo los use de primer nivel, que son importaciones; los use
                // de dentro de una clase van sangrados y son traits ya importados.
                preg_match_all('/^use\s+(?!function\s|const\s)([A-Za-z_][\w\\\\]*)/m', (string) file_get_contents($file->getPathname()), $matches);

                foreach ($matches[1] as $class) {
                    $classes[ltrim($class, '\\')] = substr(str_replace('\\', '/', $file->getPathname()), strlen(str_replace('\\', '/', $root)) + 1);
                }
            }
        }

        return $classes;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function installedPackages(string $root): array
    {
        $installed = json_decode((string) file_get_contents($root . '/vendor/composer/installed.json'), true);

        return $installed['packages'] ?? $installed;
    }

    /**
     * El paquete cuyo prefijo PSR-4 mas largo contiene la clase.
     *
     * @param  array<int, array<string, mixed>>  $packages
     * @return array<string, mixed>|null
     */
    private function owner(string $class, array $packages): ?array
    {
        $best = null;
        $length = 0;

        foreach ($packages as $package) {
            foreach (array_keys($package['autoload']['psr-4'] ?? []) as $prefix) {
                if ($prefix !== '' && str_starts_with($class, $prefix) && strlen($prefix) > $length) {
                    $best = $package;
                    $length = strlen($prefix);
                }
            }
        }

        return $best;
    }
}
