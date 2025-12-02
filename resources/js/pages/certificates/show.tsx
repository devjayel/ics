import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/certificates';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { ArrowLeft, Edit, Download, Image as ImageIcon, FileText } from 'lucide-react';

interface Rul {
    id: number;
    uuid: string;
    name: string;
    contact_number: string;
    department: string;
}

interface Certificate {
    uuid: string;
    certificate_name: string;
    file_path: string;
    rul: Rul;
    created_at: string;
}

interface ShowProps {
    certificate: Certificate;
}

const breadcrumbs = (certificate: Certificate): BreadcrumbItem[] => [
    {
        title: 'Certificates',
        href: index().url,
    },
    {
        title: certificate.certificate_name,
        href: '#',
    },
];

export default function Show({ certificate }: ShowProps) {
    const isImage = (filePath: string) => {
        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        const extension = filePath.split('.').pop()?.toLowerCase();
        return extension ? imageExtensions.includes(extension) : false;
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs(certificate)}>
            <Head title={`Certificate - ${certificate.certificate_name}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <Card>
                    <CardHeader>
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-4">
                                <Link href={index().url}>
                                    <Button variant="outline" size="icon">
                                        <ArrowLeft className="h-4 w-4" />
                                    </Button>
                                </Link>
                                <div>
                                    <CardTitle>Certificate Details</CardTitle>
                                    <CardDescription>
                                        View certificate information and file
                                    </CardDescription>
                                </div>
                            </div>
                            <div className="flex gap-2">
                                <a
                                    href={`/storage/${certificate.file_path}`}
                                    download
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    <Button variant="outline">
                                        <Download className="mr-2 h-4 w-4" />
                                        Download
                                    </Button>
                                </a>
                                <Link href={`/certificates/${certificate.uuid}/edit`}>
                                    <Button>
                                        <Edit className="mr-2 h-4 w-4" />
                                        Edit
                                    </Button>
                                </Link>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            {/* Certificate Information */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4">Certificate Information</h3>
                                <div className="grid gap-6 md:grid-cols-2">
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Certificate Name
                                        </label>
                                        <p className="mt-1 text-base font-medium">
                                            {certificate.certificate_name}
                                        </p>
                                    </div>

                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            File Type
                                        </label>
                                        <div className="mt-1 flex items-center gap-2">
                                            {isImage(certificate.file_path) ? (
                                                <>
                                                    <ImageIcon className="h-5 w-5 text-blue-600" />
                                                    <span className="text-base text-blue-600">Image</span>
                                                </>
                                            ) : (
                                                <>
                                                    <FileText className="h-5 w-5 text-gray-600" />
                                                    <span className="text-base text-gray-600">Document</span>
                                                </>
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Created At
                                        </label>
                                        <p className="mt-1 text-base">
                                            {new Date(certificate.created_at).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            })}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* RUL Information */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4">Resource Unit Leader</h3>
                                <div className="grid gap-6 md:grid-cols-2">
                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Name
                                        </label>
                                        <p className="mt-1 text-base font-medium">
                                            {certificate.rul.name}
                                        </p>
                                    </div>

                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Contact Number
                                        </label>
                                        <p className="mt-1 text-base">
                                            {certificate.rul.contact_number}
                                        </p>
                                    </div>

                                    <div>
                                        <label className="text-sm font-medium text-gray-500">
                                            Department
                                        </label>
                                        <p className="mt-1 text-base">
                                            {certificate.rul.department}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {/* Certificate Preview */}
                            <div>
                                <h3 className="text-lg font-semibold mb-4">Certificate Preview</h3>
                                {isImage(certificate.file_path) ? (
                                    <div className="border rounded-lg p-4 bg-gray-50">
                                        <img
                                            src={`/storage/${certificate.file_path}`}
                                            alt={certificate.certificate_name}
                                            className="w-full h-auto rounded-lg shadow-md"
                                        />
                                    </div>
                                ) : (
                                    <div className="border rounded-lg p-8 bg-gray-50 text-center">
                                        <FileText className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                                        <p className="text-gray-600 mb-4">
                                            Preview not available for this file type
                                        </p>
                                        <a
                                            href={`/storage/${certificate.file_path}`}
                                            download
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            <Button>
                                                <Download className="mr-2 h-4 w-4" />
                                                Download File
                                            </Button>
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
