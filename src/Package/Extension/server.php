<?php

declare(strict_types=1);

namespace Package\Extension;

use StaticPHP\Attribute\Package\CustomPhpConfigureArg;
use StaticPHP\Attribute\Package\Extension;
use StaticPHP\Package\PackageInstaller;
use StaticPHP\Package\PhpExtensionPackage;

#[Extension('server')]
class server extends PhpExtensionPackage
{
    #[CustomPhpConfigureArg('Darwin')]
    #[CustomPhpConfigureArg('Linux')]
    public function getUnixConfigureArg(bool $shared, PackageInstaller $installer): string
    {
        $arg = '--enable-http-server' . ($shared ? '=shared' : '');
        $arg .= ' --enable-http2';

        if ($openssl = $installer->getLibraryPackage('openssl')) {
            $arg .= ' --with-openssl="' . $openssl->getBuildRootPath() . '"';
        }
        if ($nghttp2 = $installer->getLibraryPackage('nghttp2')) {
            $arg .= ' --with-nghttp2="' . $nghttp2->getBuildRootPath() . '"';
        }

        $nghttp3 = $installer->getLibraryPackage('nghttp3');
        $ngtcp2 = $installer->getLibraryPackage('ngtcp2');
        if ($nghttp3 && $ngtcp2) {
            $arg .= ' --enable-http3';
            $arg .= ' --with-nghttp3="' . $nghttp3->getBuildRootPath() . '"';
            $arg .= ' --with-ngtcp2="' . $ngtcp2->getBuildRootPath() . '"';
        } else {
            $arg .= ' --disable-http3';
        }

        return $arg;
    }
}
