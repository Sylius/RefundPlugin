<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Element;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class PdfDownloadElement extends Element implements PdfDownloadElementInterface
{
    public function isPdfFileDownloaded(): bool
    {
        $session = $this->getSession();
        $headers = $session->getResponseHeaders();

        return
            200 === $session->getStatusCode() &&
            'application/pdf' === $headers['content-type'][0]
        ;
    }

}
