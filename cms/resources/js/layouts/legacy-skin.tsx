import type { PropsWithChildren } from 'react';

/**
 * Phase 2 skin for any future Inertia page that must match PHPRetro.
 * Gallery URLs are frozen string literals — never import these files through Vite.
 */
export function LegacySkin({ children }: PropsWithChildren) {
    return (
        <>
            <link
                rel="stylesheet"
                href="/web-gallery/v2/styles/style.css"
            />
            <script src="/web-gallery/static/js/common.js" />
            {children}
        </>
    );
}
