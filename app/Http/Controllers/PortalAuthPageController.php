<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalAuthPageController extends Controller
{
    public function login(Request $request): View
    {
        return view('auth.portal.login', $this->pageData($request));
    }

    public function verifyEmail(Request $request): View
    {
        return view('auth.portal.verify-email', $this->pageData($request));
    }

    public function verifyCode(Request $request): View
    {
        return view('auth.portal.verify-code', $this->pageData($request));
    }

    public function resetPassword(Request $request): View
    {
        return view('auth.portal.reset-password', $this->pageData($request));
    }

    /**
     * @return array{
     *   portalKey:string,
     *   portalConfig:array{
     *     label:string,
     *     dashboard:string,
     *     requires_all:bool,
     *     roles:array<int, string>
     *   },
     *   portalView:array{display:string,asset:string},
     *   portalOptions:array<int, array{key:string,display:string,asset:string}>
     * }
     */
    protected function pageData(Request $request): array
    {
        $portal = $this->resolvePortal($request);
        $portalConfig = AuthService::portalMap()[$portal] ?? null;
        $portalView = AuthService::portalView($portal);

        abort_if($portalConfig === null || $portalView === null, 404);

        return [
            'portalKey' => $portal,
            'portalConfig' => $portalConfig,
            'portalView' => $portalView,
            'portalOptions' => collect(AuthService::portalViewMap())
                ->map(
                    static fn (array $view, string $key): array => [
                        'key' => $key,
                        'display' => $view['display'],
                        'asset' => $view['asset'],
                    ],
                )
                ->values()
                ->all(),
        ];
    }

    protected function resolvePortal(Request $request): string
    {
        $portal = (string) $request->query('portal', 'admin');

        return array_key_exists($portal, AuthService::portalMap()) ? $portal : 'admin';
    }
}
