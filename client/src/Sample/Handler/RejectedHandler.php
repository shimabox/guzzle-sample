<?php

namespace App\Sample\Handler;

use App\Sample\RejectedHandlerInterface;
use Psr\Http\Client\ClientExceptionInterface;

class RejectedHandler implements RejectedHandlerInterface
{
    private array $result = [];

    /**
     * ClientExceptionInterfaceはConnectException は、
     * GuzzleHttp\Exception\ConnectException または、
     * GuzzleHttp\Exception\RequestException が実装している
     *
     * GuzzleHttp\Exception\ConnectException
     * - HTTPリクエストをサーバーに送信しようとした際に、接続レベルでの問題が発生した場合に投げられる
     * - 例えば、DNSの解決に失敗した、接続がタイムアウトした、ネットワークがダウンしているなどの状況がこれに該当する
     *
     * GuzzleHttp\Exception\RequestException
     * - HTTPリクエストが送信された後に何らかの問題が発生した場合に投げられる例外
     * - これには、無効な応答を受け取った場合や、クライアントやサーバー側のエラーが発生した場合などが含まれる
     * - ConnectException以外の例外は、RequestExceptionがスーパークラスなので問題ない
     *
     * なので、例外の種類によって使い分けるならインスタンスの型を見る必要がある
     *
     * @param ClientExceptionInterface $e
     * @param string $key
     * @return void
     * @see https://docs.guzzlephp.org/en/latest/quickstart.html#exceptions
     */
    public function handle(ClientExceptionInterface $e, string $key): void
    {
        $this->result[$key] = [
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
        ];
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
