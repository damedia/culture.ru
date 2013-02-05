<?php
namespace Armd\MainBundle\Ajax;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * This class is designed for use in ajax proxy actions.
 *
 * For example this way:
 *    public function communicationPlatformRequestAction()
 *    {
 *        return $this->container->get('armd_main.ajax_proxy')->ajaxRequest(
 *            $this->container->getParameter('communication_platform_domain'),
 *            $this->getRequest()
 *        );
 *    }
 *
 *
 *
 * JavaScript example:
 *
 * function sendAjaxRequest(){
 *    $.ajax({
 *        type: "POST",
 *        dataType: 'json',
 *        url: "{{ path('_ajaxProxy') }}",
 *        data: {
 *            restUrl : "/external-api-url/",
 *            method: 'POST',
 *            params: {
 *                action: "getFriendList"
 *            }
 *        },
 *        success: function(data, textStatus, jqXHR) {
 *            ...
 *        }
 *    });
 *}
 *
 */

class AjaxProxy
{

    function ajaxRequest($baseHost, Request $request)
    {
        // Forbid every request but jquery's XHR
//        if (!$request->isXmlHttpRequest()) { // isn't it an Ajax request?
//            return new Response('', 404,
//                array('Content-Type' => 'application/json'));
//        }

        $restUrl = 'http://' . $baseHost . $request->get('restUrl');
        $method = $request->getMethod();
        $params = $request->get('params');
        $contentType = $request->get('contentType');

        if ($contentType == null) {
            $contentType = 'application/json';
        }

        if ($restUrl == null || $method == null ||
            !in_array($method, array('GET', 'POST', 'DELETE'))
        ) {
            return new Response('', 404, array('Content-Type' => $contentType));
        }

        session_write_close();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $restUrl);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($params != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $requestCookies = $request->cookies->all();

        $cookieArray = array();
        foreach ($requestCookies as $cookieName => $cookieValue) {
            if($cookieName !== $request->getSession()->getName()) {
                $cookieArray[] = urlencode($cookieName) . "=" . urlencode($cookieValue);
            }
        }
        $cookie_string = implode('; ', $cookieArray);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie_string);

        $response = curl_exec($ch);

        curl_close($ch);

        list($headers, $response) = explode("\r\n\r\n", $response, 2);
        preg_match_all('/Set-Cookie: (.*)\b/', $headers, $cookies);
        $cookies = $cookies[1];

        if ($response === false) {
            return new Response('', 404, array('Content-Type' => $contentType));
        } else {
            $response = new Response($response, 200,
                array('Content-Type' => $contentType));
            foreach ($cookies as $rawCookie) {
                $cookie = \Symfony\Component\BrowserKit\Cookie::fromString($rawCookie);
                $value = $cookie->getValue();
                if (!empty($value)) {
                    $value = str_replace(' ', '+', $value);
                }
                $customCookie = new Cookie($cookie->getName(), $value, $cookie->getExpiresTime(
                ) == null ? 0 : $cookie->getExpiresTime(), $cookie->getPath());
                $response->headers->setCookie($customCookie);
            }

            return $response;
        }

    }

}